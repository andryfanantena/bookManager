<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Services\BookCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_catalog_metadata_when_scanning_an_isbn(): void
    {
        $isbn = '9782070368228';

        $this->mock(BookCatalogService::class)
            ->shouldReceive('findByIsbn')
            ->once()
            ->with($isbn)
            ->andReturn([
                'source' => 'Google Books',
                'data' => [
                    'titre' => '1984',
                    'auteur' => 'George Orwell',
                    'editeur' => 'Gallimard',
                    'date_edition' => '1993',
                    'lien_editeur' => 'https://books.google.test/volume',
                ],
                'context' => [
                    'categories' => ['Fiction'],
                ],
                'raw' => [],
            ]);

        $response = $this->post(route('scan.analyze'), ['isbn' => $isbn]);

        $book = Book::query()->where('isbn', $isbn)->firstOrFail();

        $response->assertRedirect(route('books.edit', $book));
        $this->assertSame('1984', $book->titre);
        $this->assertSame('George Orwell', $book->auteur);
        $this->assertSame('Gallimard', $book->editeur);
        $this->assertSame('1993', $book->date_edition);
        $this->assertSame('https://books.google.test/volume', $book->lien_editeur);
        $this->assertSame('Fiction', $book->category->nom);
    }
}
