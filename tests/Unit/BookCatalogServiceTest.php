<?php

namespace Tests\Unit;

use App\Services\BookCatalogService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookCatalogServiceTest extends TestCase
{
    public function test_it_falls_back_to_google_books_when_open_library_has_no_match(): void
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
        $this->assertSame(['Juvenile Fiction'], $result['context']['categories']);
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
        Http::assertSentCount(3);
    }

    public function test_it_prefers_open_library_and_maps_edition_metadata(): void
    {
        Http::fake([
            'openlibrary.org/isbn/*' => Http::response([
                'key' => '/books/OL123M',
                'title' => 'Le Petit Prince',
                'authors' => [['key' => '/authors/OL1A']],
                'publishers' => ['Gallimard'],
                'publish_date' => '1943',
                'subjects' => ['Fiction'],
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
        $this->assertSame(['Fiction'], $result['context']['categories']);
        Http::assertSentCount(2);
    }
}
