<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Recherche les informations bibliographiques d'un livre à partir de son ISBN.
 *
 * - Google Books fournit les informations principales (titre, auteur,
 *   éditeur, date d'édition, lien éditeur, catégories).
 * - Open Library est interrogé spécifiquement pour la cote de classification
 *   Dewey (champ "dewey_decimal_class" des éditions cataloguées), en
 *   remplacement de l'API OCLC Classify, définitivement fermée le
 *   31/01/2024 et qui n'est plus accessible, gratuitement ou non.
 * - Si Google Books ne trouve rien, Open Library sert aussi de repli complet
 *   pour les informations bibliographiques (meilleure résilience).
 * - Gallica (BnF) est consulté en dernier recours lorsqu'aucune autre source
 *   ne trouve le livre.
 */
class BookCatalogService
{
    public function findByIsbn(string $isbn): ?array
    {
        $googleBooks = $this->findInGoogleBooks($isbn);

        if ($googleBooks) {
            // Google Books a réussi : on ne fait qu'un appel léger et dédié
            // à Open Library pour récupérer uniquement la cote Dewey, sans
            // perdre de temps à résoudre les noms d'auteurs qu'on a déjà.
            $dewey = $this->findDeweyInOpenLibrary($isbn);

            $data = $googleBooks['data'];
            $sources = [$googleBooks['source']];

            if ($dewey) {
                $data['cote_dewey'] = $dewey['cote_dewey'];
                $sources[] = 'Open Library (cote Dewey)';
            }

            return [
                'source' => implode(' + ', $sources),
                'data' => $data,
                'context' => $googleBooks['context'],
                'raw' => [
                    'google_books' => $googleBooks['raw'],
                    'open_library' => $dewey['raw'] ?? null,
                ],
            ];
        }

        // Google Books n'a rien trouvé : Open Library sert de repli complet
        // (titre, auteur, éditeur... et cote Dewey si disponible).
        $openLibrary = $this->findInOpenLibrary($isbn);

        if ($openLibrary) {
            return [
                'source' => $openLibrary['source'],
                'data' => $openLibrary['data'],
                'context' => $openLibrary['context'],
                'raw' => [
                    'google_books' => null,
                    'open_library' => $openLibrary['raw'],
                ],
            ];
        }

        $gallica = $this->findInGallica($isbn);

        return $gallica ? [
            'source' => $gallica['source'],
            'data' => $gallica['data'],
            'context' => $gallica['context'],
            'raw' => [
                'google_books' => null,
                'open_library' => null,
                'gallica' => $gallica['raw'],
            ],
        ] : null;
    }

    /**
     * Lookup léger auprès d'Open Library : uniquement la cote Dewey, sans
     * résoudre les auteurs (inutile quand Google Books a déjà répondu).
     * Remplace l'API OCLC Classify, fermée depuis le 31/01/2024.
     */
    protected function findDeweyInOpenLibrary(string $isbn): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(config('app.name', 'BookManager').'/1.0 (ISBN catalog lookup)')
                ->timeout(8)
                ->get('https://openlibrary.org/isbn/'.rawurlencode($isbn).'.json');

            if ($response->failed()) {
                return null;
            }

            $edition = $response->json();
            if (! is_array($edition)) {
                return null;
            }

            $cote = $this->extractDeweyClassification($edition);

            return $cote ? ['cote_dewey' => $cote, 'raw' => $edition] : null;
        } catch (Throwable $e) {
            Log::debug('Open Library Dewey lookup failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    protected function findInGoogleBooks(string $isbn): ?array
    {
        try {
            $query = [
                'q' => 'isbn:'.$isbn,
                'maxResults' => 1,
            ];

            if ($apiKey = config('services.google_books.api_key')) {
                $query['key'] = $apiKey;
            }

            $response = Http::retry(2, 250)
                ->acceptJson()
                ->timeout(8)
                ->get('https://www.googleapis.com/books/v1/volumes', $query);

            if ($response->failed()) {
                Log::warning('Google Books lookup failed', ['status' => $response->status()]);

                return null;
            }

            $volume = $response->json('items.0');
            $info = $volume['volumeInfo'] ?? null;

            if (! is_array($info) || empty($info['title'])) {
                return null;
            }

            $authors = array_values(array_filter($info['authors'] ?? [], 'is_string'));
            $categories = array_values(array_filter($info['categories'] ?? [], 'is_string'));
            $title = trim(($info['title'] ?? '').(empty($info['subtitle']) ? '' : ': '.$info['subtitle']));
            $context = [
                'titre' => $title,
                'auteur' => implode(', ', $authors),
                'editeur' => $info['publisher'] ?? '',
                'date_edition' => $info['publishedDate'] ?? '',
                'description' => mb_substr((string) ($info['description'] ?? ''), 0, 3000),
                'categories' => $categories,
            ];

            // Google Books fournit presque toujours une miniature de
            // couverture ; on préfère la version "thumbnail" (plus grande
            // que "smallThumbnail") et on force le HTTPS (l'API renvoie
            // parfois du http:// brut).
            $thumbnail = $info['imageLinks']['thumbnail']
                ?? $info['imageLinks']['smallThumbnail']
                ?? null;
            $coverImage = $thumbnail ? preg_replace('/^http:/', 'https:', $thumbnail) : null;

            return [
                'source' => 'Google Books',
                'data' => [
                    'titre' => $context['titre'],
                    'auteur' => $context['auteur'],
                    'editeur' => $context['editeur'],
                    'date_edition' => $context['date_edition'],
                    'lien_editeur' => $info['infoLink'] ?? null,
                    'cover_image' => $coverImage,
                ],
                'context' => $context,
                'raw' => $volume,
            ];
        } catch (Throwable $e) {
            Log::warning('Google Books request error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    protected function findInOpenLibrary(string $isbn): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent(config('app.name', 'BookManager').'/1.0 (ISBN catalog lookup)')
                ->timeout(8)
                ->get('https://openlibrary.org/isbn/'.rawurlencode($isbn).'.json');

            if ($response->failed()) {
                return null;
            }

            $edition = $response->json();
            if (! is_array($edition) || empty($edition['title'])) {
                return null;
            }

            $authors = [];
            foreach (array_slice($edition['authors'] ?? [], 0, 3) as $author) {
                $authorKey = $author['key'] ?? null;
                if (! $authorKey) {
                    continue;
                }

                try {
                    $authorResponse = Http::acceptJson()
                        ->withUserAgent(config('app.name', 'BookManager').'/1.0 (ISBN catalog lookup)')
                        ->timeout(4)
                        ->get('https://openlibrary.org'.$authorKey.'.json');

                    if ($authorResponse->successful() && $name = $authorResponse->json('name')) {
                        $authors[] = $name;
                    }
                } catch (Throwable $e) {
                    Log::debug('Open Library author lookup failed', ['author' => $authorKey]);
                }
            }

            $publishers = array_values(array_filter($edition['publishers'] ?? [], 'is_string'));
            $subjects = array_values(array_filter($edition['subjects'] ?? [], 'is_string'));
            $context = [
                'titre' => $edition['title'],
                'auteur' => implode(', ', $authors),
                'editeur' => implode(', ', $publishers),
                'date_edition' => $edition['publish_date'] ?? '',
                'description' => '',
                'categories' => array_slice($subjects, 0, 10),
            ];

            return [
                'source' => 'Open Library',
                'data' => [
                    'titre' => $context['titre'],
                    'auteur' => $context['auteur'],
                    'editeur' => $context['editeur'],
                    'date_edition' => $context['date_edition'],
                    'lien_editeur' => 'https://openlibrary.org'.($edition['key'] ?? '/isbn/'.$isbn),
                    'cote_dewey' => $this->extractDeweyClassification($edition),
                ],
                'context' => $context,
                'raw' => $edition,
            ];
        } catch (Throwable $e) {
            Log::warning('Open Library request error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    protected function findInGallica(string $isbn): ?array
    {
        try {
            $response = Http::retry(2, 250)
                ->accept('application/xml')
                ->withUserAgent(config('app.name', 'BookManager').'/1.0 (ISBN catalog lookup)')
                ->timeout(8)
                ->get(config('services.gallica.base_url'), [
                    'version' => '1.2',
                    'operation' => 'searchRetrieve',
                    'query' => 'dc.identifier all "'.$isbn.'"',
                    'maximumRecords' => 1,
                ]);

            if ($response->failed()) {
                Log::warning('Gallica lookup failed', ['status' => $response->status()]);

                return null;
            }

            $document = new \DOMDocument;
            if (! $document->loadXML($response->body(), LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
                Log::warning('Gallica returned an invalid XML response');

                return null;
            }

            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('srw', 'http://www.loc.gov/zing/srw/');
            $xpath->registerNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
            $xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');

            $record = $xpath->query('/srw:searchRetrieveResponse/srw:records/srw:record[1]')?->item(0);
            if (! $record instanceof \DOMElement) {
                return null;
            }

            $value = static function (string $expression) use ($xpath, $record): string {
                return trim($xpath->evaluate('string('.$expression.')', $record) ?: '');
            };

            $title = $value('./srw:recordData/oai_dc:dc/dc:title[1]');
            if ($title === '') {
                return null;
            }

            $link = $value('./srw:extraRecordData/*[local-name()="link"]');
            $identifier = $value('./srw:recordData/oai_dc:dc/dc:identifier[1]');
            $link = str_starts_with($link, 'https://gallica.bnf.fr/')
                ? $link
                : (str_starts_with($identifier, 'https://gallica.bnf.fr/') ? $identifier : null);
            $thumbnail = $value('./srw:extraRecordData/*[local-name()="thumbnail"]');
            $thumbnail = str_starts_with($thumbnail, 'https://gallica.bnf.fr/') ? $thumbnail : null;
            $authors = [];
            foreach ($xpath->query('./srw:recordData/oai_dc:dc/dc:creator', $record) ?: [] as $author) {
                $authorName = trim($author->textContent);
                if ($authorName !== '') {
                    $authors[] = $authorName;
                }
            }

            $context = [
                'titre' => $title,
                'auteur' => implode(', ', $authors),
                'editeur' => $value('./srw:recordData/oai_dc:dc/dc:publisher[1]'),
                'date_edition' => $value('./srw:recordData/oai_dc:dc/dc:date[1]'),
                'description' => '',
                'categories' => [],
            ];

            return [
                'source' => 'Gallica (BnF)',
                'data' => [
                    'titre' => $context['titre'],
                    'auteur' => $context['auteur'],
                    'editeur' => $context['editeur'],
                    'date_edition' => $context['date_edition'],
                    'lien_editeur' => $link,
                    'cover_image' => $thumbnail,
                ],
                'context' => $context,
                'raw' => $record->C14N(),
            ];
        } catch (Throwable $e) {
            Log::warning('Gallica request error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Extrait et normalise la cote Dewey depuis une fiche d'édition Open
     * Library. Le champ "dewey_decimal_class" est un tableau (une édition
     * peut avoir plusieurs cotes attribuées par différents catalogueurs) ;
     * on retient la première, qui est généralement la plus fiable.
     *
     * La notation Open Library utilise parfois un "/" (ex: "813/.54") pour
     * indiquer un numéro "construit" ; on le retire pour obtenir une cote
     * directement utilisable pour le rangement ("813.54").
     */
    protected function extractDeweyClassification(array $edition): ?string
    {
        $classes = $edition['dewey_decimal_class'] ?? [];

        if (! is_array($classes) || empty($classes)) {
            return null;
        }

        $first = (string) reset($classes);

        return str_replace('/', '', trim($first)) ?: null;
    }
}
