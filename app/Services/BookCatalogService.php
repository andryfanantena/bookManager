<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookCatalogService
{
    public function findByIsbn(string $isbn): ?array
    {
        return $this->findInOpenLibrary($isbn)
            ?? $this->findInGoogleBooks($isbn);
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

            return [
                'source' => 'Google Books',
                'data' => [
                    'titre' => $context['titre'],
                    'auteur' => $context['auteur'],
                    'editeur' => $context['editeur'],
                    'date_edition' => $context['date_edition'],
                    'lien_editeur' => $info['infoLink'] ?? null,
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
                ],
                'context' => $context,
                'raw' => $edition,
            ];
        } catch (Throwable $e) {
            Log::warning('Open Library request error', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
