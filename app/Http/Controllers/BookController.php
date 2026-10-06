<?php

namespace App\Http\Controllers;

use App\Exports\BooksExport;
use App\Models\Book;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    /**
     * Catalogue complet, avec recherche et filtres (page "Liste des livres").
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                    ->orWhere('auteur', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('cote_dewey', 'like', "%{$search}%")
                    ->orWhere('code_barre', 'like', "%{$search}%");
            });
        }

        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $categories = Category::orderBy('nom')->get();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Page de vérification / modification détaillée d'un livre.
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('nom')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Enregistre les modifications et marque le livre comme "validé".
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'editeur' => ['nullable', 'string', 'max:255'],
            'date_edition' => ['nullable', 'string', 'max:50'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'code_barre' => ['nullable', 'string', 'max:50'],
            'cote_dewey' => ['nullable', 'string', 'max:20'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'nombre_exemplaires' => ['nullable', 'integer', 'min:0'],
            'couverture_couleur' => ['nullable', 'string', 'max:100'],
            'lien_editeur' => ['nullable', 'url', 'max:255'],
            'rayon' => ['nullable', 'string', 'max:100'],
            'chapitres' => ['nullable', 'array'],
            'chapitres.*' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:10240'],
            'action' => ['required', 'in:save_draft,validate'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        // On retire les entrées vides de la liste des chapitres
        $validated['chapitres'] = collect($request->input('chapitres', []))
            ->filter(fn ($c) => filled($c))
            ->values()
            ->all();

        $validated['statut'] = $validated['action'] === 'validate' ? 'valide' : 'a_verifier';
        unset($validated['action']);

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', $book->statut === 'valide'
                ? "« {$book->titre} » a été enregistré et validé."
                : "« {$book->titre} » a été enregistré (toujours en attente de vérification).");
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return back()->with('success', 'Livre supprimé.');
    }

    /**
     * Export PDF du catalogue (respecte les filtres de recherche actifs).
     */
    public function exportPdf(Request $request)
    {
        $books = $this->filteredBooksQuery($request)->get();

        $pdf = Pdf::loadView('books.pdf', compact('books'))->setPaper('a4', 'landscape');

        return $pdf->download('catalogue-livres-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export Excel du catalogue (respecte les filtres de recherche actifs).
     * Nécessite le package maatwebsite/excel (voir README).
     */
    public function exportExcel(Request $request)
    {
        $books = $this->filteredBooksQuery($request)->get();

        return Excel::download(new BooksExport($books), 'catalogue-livres-'.now()->format('Y-m-d').'.xlsx');
    }

    protected function filteredBooksQuery(Request $request)
    {
        $query = Book::with('category');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                    ->orWhere('auteur', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        return $query->orderBy('cote_dewey');
    }
}
