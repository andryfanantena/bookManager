<?php

namespace Tests\Unit;

use App\Services\BookCatalogService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookCatalogServiceTest extends TestCase
{
    public function test_it_uses_google_books_as_the_primary_source(): void
    {
        Http::fake([
            'openlibrary.org/isbn/*' => Http::response([], 404),
            'www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'Le Petit Prince',
                            'authors' => ['Antoine de Saint-Exupéry'],
                            'publisher' => 'Gallimard',
                            'publishedDate' => '1943',
                            'categories' => ['Juvenile Fiction'],
                            'infoLink' => 'https://books.google.test/volume',
                            'imageLinks' => ['thumbnail' => 'http://books.google.test/cover.jpg'],
                        ],
                    ],
                ],
            ]),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertSame('Google Books', $result['source']);
        $this->assertSame('Le Petit Prince', $result['data']['titre']);
        $this->assertSame('Antoine de Saint-Exupéry', $result['data']['auteur']);
        $this->assertSame('Gallimard', $result['data']['editeur']);
        // Le thumbnail http:// doit être converti en https://
        $this->assertSame('https://books.google.test/cover.jpg', $result['data']['cover_image']);
        $this->assertSame(['Juvenile Fiction'], $result['context']['categories']);
        // Pas de cote Dewey trouvée sur Open Library : elle reste absente.
        $this->assertArrayNotHasKey('cote_dewey', $result['data']);
    }

    public function test_it_merges_the_dewey_classification_from_open_library(): void
    {
        Http::fake([
            'openlibrary.org/isbn/*' => Http::response([
                'title' => '1984',
                'dewey_decimal_class' => ['823/.912'],
            ], 200),
            'www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => '1984',
                            'authors' => ['George Orwell'],
                            'publisher' => 'Secker & Warburg',
                            'publishedDate' => '1949',
                        ],
                    ],
                ],
            ]),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertSame('Google Books + Open Library (cote Dewey)', $result['source']);
        $this->assertSame('1984', $result['data']['titre']);
        $this->assertSame('George Orwell', $result['data']['auteur']);
        // Le "/" de la notation Open Library est retiré pour le rangement.
        $this->assertSame('823.912', $result['data']['cote_dewey']);
    }

    public function test_it_retries_google_books_after_a_temporary_server_error(): void
    {
        Http::fake([
            'openlibrary.org/isbn/*' => Http::response([], 404),
            'www.googleapis.com/books/v1/volumes*' => Http::sequence()
                ->push(['error' => ['message' => 'Temporary outage']], 503)
                ->push([
                    'items' => [
                        [
                            'volumeInfo' => [
                                'title' => '1984',
                                'authors' => ['George Orwell'],
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertSame('Google Books', $result['source']);
        $this->assertSame('1984', $result['data']['titre']);
    }

    public function test_it_falls_back_to_open_library_when_google_books_has_no_match(): void
    {
        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
            'openlibrary.org/isbn/*' => Http::response([
                'key' => '/books/OL123M',
                'title' => 'Le Petit Prince',
                'authors' => [['key' => '/authors/OL1A']],
                'publishers' => ['Gallimard'],
                'publish_date' => '1943',
                'subjects' => ['Fiction'],
                'dewey_decimal_class' => ['843'],
            ], 200),
            'openlibrary.org/authors/OL1A.json' => Http::response([
                'name' => 'Antoine de Saint-Exupéry',
            ], 200),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertSame('Open Library', $result['source']);
        $this->assertSame('Le Petit Prince', $result['data']['titre']);
        $this->assertSame('Antoine de Saint-Exupéry', $result['data']['auteur']);
        $this->assertSame('Gallimard', $result['data']['editeur']);
        $this->assertSame('1943', $result['data']['date_edition']);
        $this->assertSame('843', $result['data']['cote_dewey']);
        $this->assertSame(['Fiction'], $result['context']['categories']);
    }

    public function test_it_falls_back_to_gallica_when_google_books_and_open_library_have_no_match(): void
    {
        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
            'openlibrary.org/isbn/*' => Http::response([], 404),
            'gallica.bnf.fr/SRU*' => Http::response(<<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <srw:searchRetrieveResponse
                    xmlns:srw="http://www.loc.gov/zing/srw/"
                    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                    xmlns:dc="http://purl.org/dc/elements/1.1/">
                    <srw:records>
                        <srw:record>
                            <srw:recordData>
                                <oai_dc:dc>
                                    <dc:date>1900</dc:date>
                                    <dc:creator>Victor Hugo</dc:creator>
                                    <dc:identifier>https://gallica.bnf.fr/ark:/12148/bpt6k123</dc:identifier>
                                    <dc:publisher>Éditeur historique</dc:publisher>
                                    <dc:title>Les Misérables</dc:title>
                                </oai_dc:dc>
                            </srw:recordData>
                            <srw:extraRecordData>
                                <link>https://gallica.bnf.fr/ark:/12148/bpt6k123</link>
                                <thumbnail>https://gallica.bnf.fr/ark:/12148/bpt6k123.thumbnail</thumbnail>
                            </srw:extraRecordData>
                        </srw:record>
                    </srw:records>
                </srw:searchRetrieveResponse>
                XML, 200, ['Content-Type' => 'application/xml']),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertSame('Gallica (BnF)', $result['source']);
        $this->assertSame('Les Misérables', $result['data']['titre']);
        $this->assertSame('Victor Hugo', $result['data']['auteur']);
        $this->assertSame('Éditeur historique', $result['data']['editeur']);
        $this->assertSame('1900', $result['data']['date_edition']);
        $this->assertSame('https://gallica.bnf.fr/ark:/12148/bpt6k123', $result['data']['lien_editeur']);
        $this->assertSame('https://gallica.bnf.fr/ark:/12148/bpt6k123.thumbnail', $result['data']['cover_image']);
        $this->assertSame([], $result['context']['categories']);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'gallica.bnf.fr/SRU')
            && str_contains($request->url(), '9780306406157'));
    }

    public function test_it_returns_null_when_nothing_is_found_anywhere(): void
    {
        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
            'openlibrary.org/isbn/*' => Http::response([], 404),
            'gallica.bnf.fr/SRU*' => Http::response(
                '<srw:searchRetrieveResponse xmlns:srw="http://www.loc.gov/zing/srw/"><srw:numberOfRecords>0</srw:numberOfRecords></srw:searchRetrieveResponse>',
                200,
                ['Content-Type' => 'application/xml']
            ),
        ]);

        $result = app(BookCatalogService::class)->findByIsbn('9780306406157');

        $this->assertNull($result);
    }
}
