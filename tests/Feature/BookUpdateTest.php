<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_a_book_preserves_its_id_and_does_not_create_another_row(): void
    {
        $this->actingAs(User::factory()->create());

        $book = Book::create([
            'titre' => 'Titre initial',
            'isbn' => '9782070368228',
            'statut' => 'a_verifier',
        ]);

        $response = $this->put(route('books.update', $book), [
            'titre' => 'Titre modifié',
            'action' => 'validate',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseCount('books', 1);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'titre' => 'Titre modifié',
            'isbn' => '9782070368228',
            'statut' => 'valide',
        ]);
    }

    public function test_author_field_on_edit_page_is_multiline_for_long_author_lists(): void
    {
        $this->actingAs(User::factory()->create());
        $authors = 'Auteur Un, Auteur Deux, Auteur Trois, Auteur Quatre';
        $book = Book::create([
            'titre' => 'Livre avec plusieurs auteurs',
            'auteur' => $authors,
        ]);

        $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('<textarea id="auteur" name="auteur" rows="2"', false)
            ->assertSee($authors);
    }

    public function test_layout_has_a_desktop_sidebar_toggle_for_the_main_navigation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('aria-controls="primary-sidebar"', false)
            ->assertSee('data-lucide="panel-left-close"', false)
            ->assertSee('data-lucide="panel-left-open"', false)
            ->assertSee('localStorage.setItem(\'sidebarCollapsed\'', false)
            ->assertSee('sidebarReady: false', false)
            ->assertSee('sidebarReady ? \'md:transition-[width] md:duration-200\'', false)
            ->assertSee('sidebarReady ? \'md:transition-[margin] md:duration-200\'', false);
    }

    public function test_next_book_id_restarts_at_one_when_the_sqlite_books_table_is_empty(): void
    {
        Book::create(['titre' => 'Ancien livre']);

        DB::table('books')->delete();

        $book = Book::create(['titre' => 'Premier livre']);

        $this->assertSame(1, $book->id);
    }
}
