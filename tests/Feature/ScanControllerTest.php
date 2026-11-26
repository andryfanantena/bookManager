<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Services\BookCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_it_saves_catalog_metadata_when_scanning_an_isbn(): void
    {
        $isbn = '9782070368228';
        Storage::fake('public');

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
                    'date_edition' => '1993-10-31',
                    'chapitres' => ['Chapitre 1 : La ferme des animaux'],
                    'lien_editeur' => 'https://books.google.test/volume',
                    'cover_image' => 'https://covers.openlibrary.org/b/id/12345-L.jpg',
                ],
                'context' => [
                    'categories' => ['Fiction'],
                ],
                'raw' => [],
            ]);
        app(BookCatalogService::class)
            ->shouldReceive('downloadCover')
            ->once()
            ->with('https://covers.openlibrary.org/b/id/12345-L.jpg', $isbn)
            ->andReturn('covers/catalog/'.$isbn.'.jpg');

        $response = $this->post(route('scan.analyze'), ['isbn' => $isbn]);

        $book = Book::query()->where('isbn', $isbn)->firstOrFail();

        $response->assertRedirect(route('books.edit', $book));
        $this->assertSame('1984', $book->titre);
        $this->assertSame('George Orwell', $book->auteur);
        $this->assertSame('Gallimard', $book->editeur);
        $this->assertSame('1993', $book->date_edition);
        $this->assertSame('https://books.google.test/volume', $book->lien_editeur);
        $this->assertSame('covers/catalog/'.$isbn.'.jpg', $book->cover_image);
        $this->assertSame(['Chapitre 1 : La ferme des animaux'], $book->mots_cles);
        $this->assertSame(['Chapitre 1 : La ferme des animaux'], $book->chapitres);
        $this->assertSame('Fiction', $book->category->nom);
    }

    public function test_scanning_an_existing_isbn_opens_the_existing_book_without_creating_a_duplicate(): void
    {
        $book = Book::create([
            'titre' => 'Livre déjà catalogué',
            'isbn' => '978-2-07-036822-8',
            'statut' => 'valide',
        ]);

        $this->mock(BookCatalogService::class)
            ->shouldNotReceive('findByIsbn');

        $response = $this->post(route('scan.analyze'), ['isbn' => '978 2 07 036822 8']);

        $response->assertRedirect(route('books.edit', $book))
            ->assertSessionHas('warning', 'Un livre avec cet ISBN existe déjà. Vous pouvez modifier sa fiche.');

        $this->assertSame(1, Book::query()->count());
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'titre' => 'Livre déjà catalogué',
            'isbn' => '978-2-07-036822-8',
        ]);
    }

    public function test_failed_cover_download_does_not_show_a_warning(): void
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
                    'cover_image' => 'https://covers.openlibrary.org/b/id/12345-L.jpg',
                ],
                'context' => [],
                'raw' => [],
            ])
            ->shouldReceive('downloadCover')
            ->once()
            ->with('https://covers.openlibrary.org/b/id/12345-L.jpg', $isbn)
            ->andReturn(null);

        $response = $this->post(route('scan.analyze'), ['isbn' => $isbn]);

        $book = Book::query()->where('isbn', $isbn)->firstOrFail();

        $response->assertRedirect(route('books.edit', $book))
            ->assertSessionHas('success', 'Livre trouvé dans Google Books. Merci de vérifier les informations avant de valider.')
            ->assertSessionMissing('warning');
    }

    public function test_recent_scan_results_wrap_long_author_lists(): void
    {
        $authors = 'Auteur Un, Auteur Deux, Auteur Trois, Auteur Quatre';
        Book::create([
            'titre' => 'Livre avec plusieurs auteurs',
            'auteur' => $authors,
        ]);

        $this->get(route('scan.index'))
            ->assertOk()
            ->assertSee($authors)
            ->assertSee('whitespace-normal break-words mt-0.5', false)
            ->assertSee('items-start gap-3', false);
    }
}
