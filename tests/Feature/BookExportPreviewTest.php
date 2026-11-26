<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookExportPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_export_preview_shows_all_excel_columns_and_book_details(): void
    {
        $category = Category::create(['nom' => 'Roman']);
        Book::create([
            'category_id' => $category->id,
            'cote_dewey' => '840',
            'titre' => 'Le livre de test',
            'auteur' => 'Auteur Exemple',
            'editeur' => 'Éditions Test',
            'isbn' => '9780000000001',
            'statut' => 'valide',
        ]);

        $response = $this->get(route('books.export.index'));

        $response->assertOk()
            ->assertSee('Aperçu de l’export Excel')
            ->assertSee('Le livre de test')
            ->assertSee('Auteur Exemple')
            ->assertSee('Éditions Test')
            ->assertSee('Roman')
            ->assertSee('9780000000001')
            ->assertSee('Télécharger le fichier Excel');

        foreach ((new \App\Exports\BooksExport(collect()))->headings() as $heading) {
            $response->assertSee($heading);
        }
    }

    public function test_export_preview_uses_chapters_for_keywords_and_wraps_long_cell_content(): void
    {
        Book::create([
            'titre' => 'Livre avec sommaire',
            'statut' => 'valide',
            'chapitres' => ['Chapitre très long '.str_repeat('mot sans espace ', 20)],
            'mots_cles' => ['Catégorie générique'],
        ]);

        $response = $this->get(route('books.export.index'));

        $response->assertOk()
            ->assertSee('Chapitre très long')
            ->assertDontSee('Catégorie générique')
            ->assertSee('whitespace-normal break-words', false);
    }

    public function test_export_preview_numbers_books_sequentially_instead_of_showing_database_ids(): void
    {
        Book::create(['titre' => 'Livre supprimé'])->delete();
        Book::create(['titre' => 'Livre restant']);

        $response = $this->get(route('books.export.index'));

        $response->assertOk()
            ->assertSee('N°')
            ->assertSee('Livre restant');
        $this->assertSame(1, preg_match('/<td[^>]*>\s*1\s*<\/td>/', $response->getContent()));

        foreach (range(1, 25) as $number) {
            Book::create(['titre' => 'Livre '.$number]);
        }

        $secondPage = $this->get(route('books.export.index', ['page' => 2]));

        $secondPage->assertOk();
        $this->assertSame(1, preg_match('/<td[^>]*>\s*26\s*<\/td>/', $secondPage->getContent()));
    }

    public function test_export_preview_filters_books_and_keeps_filters_for_download(): void
    {
        $category = Category::create(['nom' => 'Histoire']);
        Book::create([
            'category_id' => $category->id,
            'titre' => 'Histoire de France',
            'statut' => 'valide',
        ]);
        Book::create([
            'titre' => 'Roman sans rapport',
            'statut' => 'a_verifier',
        ]);

        $response = $this->get(route('books.export.index', [
            'q' => 'France',
            'statut' => 'valide',
            'category_id' => $category->id,
        ]));

        $response->assertOk()
            ->assertSee('Histoire de France')
            ->assertDontSee('Roman sans rapport')
            ->assertSee(route('books.export.excel', [
                'q' => 'France',
                'statut' => 'valide',
                'category_id' => $category->id,
            ]));
    }

    public function test_excel_download_still_returns_a_downloadable_workbook(): void
    {
        Book::create(['titre' => 'Livre exportable']);

        $this->get(route('books.export.excel'))
            ->assertDownload('catalogue-livres-'.now()->format('Y-m-d').'.xlsx');
    }
}
