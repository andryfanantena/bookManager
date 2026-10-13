<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookExportPreviewTest extends TestCase
{
    use RefreshDatabase;

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
