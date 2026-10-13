<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookCatalogService;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Page de recherche d’un livre par ISBN.
     */
    public function index()
    {
        $recentScans = Book::latest()->limit(6)->get();

        return view('scan.index', compact('recentScans'));
    }

    /**
    * Recherche l’ISBN dans les catalogues bibliographiques. Le livre créé
    * reste à vérifier avant validation.
     */
    public function analyze(Request $request)
    {
        $request->merge([
            'isbn' => strtoupper(preg_replace('/[\s-]+/', '', (string) $request->input('isbn'))),
        ]);
        $validated = $request->validate([
            'isbn' => [
                'required',
                'string',
                'regex:/^(?:\d{9}[\dX]|\d{13})$/',
                function ($attribute, $value, $fail) {
                    if (! $this->hasValidIsbnChecksum($value)) {
                        $fail('Le chiffre de contrôle de cet ISBN est incorrect.');
                    }
                },
            ],
        ], [
            'isbn.required' => 'Veuillez saisir un ISBN.',
            'isbn.regex' => 'Saisissez un ISBN-10 ou ISBN-13 (chiffres uniquement, X autorisé en dernière position pour un ISBN-10).',
        ]);
        $isbn = $validated['isbn'];

        $catalogResult = app(BookCatalogService::class)->findByIsbn($isbn);

        if (! $catalogResult) {
            $book = Book::create([
                'titre' => 'Titre inconnu - Recherche ISBN',
                'isbn' => $isbn,
                'statut' => 'a_verifier',
            ]);

            $message = "L'analyse automatique a échoué. La fiche a été créée avec l'ISBN ; merci de la compléter manuellement.";

            if ($request->wantsJson()) {
                session()->flash('warning', $message);

                return response()->json([
                    'message' => $message,
                    'redirect' => route('books.edit', $book),
                ], 422);
            }

            return redirect()
                ->route('books.edit', $book)
                ->with('warning', $message);
        }

        $data = array_merge([
            'titre' => '',
            'auteur' => '',
            'editeur' => '',
            'date_edition' => '',
            'code_barre' => '',
            'cote_dewey' => '',
            'rubrique' => '',
            'couverture_couleur' => '',
            'rayon' => '',
            'mots_cles' => [],
            'chapitres' => [],
        ], $catalogResult['data'] ?? []);
        $catalogData = $catalogResult['data'] ?? [];

        // Les catégories du catalogue alimentent la rubrique et les mots-clés.
        $data['isbn'] = $isbn;

        $catalogCategories = $catalogResult['context']['categories'] ?? [];
        if (empty($data['rubrique'])) {
            $data['rubrique'] = $catalogCategories[0] ?? '';
        }
        $data['mots_cles'] = array_values(array_unique(array_merge(
            $catalogCategories,
            $data['mots_cles'] ?? []
        )));

        // On associe/crée la rubrique issue du classement IA ou du catalogue.
        $category = null;
        if (! empty($data['rubrique'])) {
            $category = Category::firstOrCreate(['nom' => $data['rubrique']]);
        }

        // 2. Création du livre en statut "à vérifier"
        $book = Book::create([
            'titre' => $data['titre'] ?: 'Titre inconnu - Recherche ISBN',
            'auteur' => $data['auteur'] ?: null,
            'editeur' => $data['editeur'] ?: null,
            'date_edition' => $data['date_edition'] ?: null,
            'isbn' => $isbn,
            'lien_editeur' => $catalogData['lien_editeur'] ?? null,
            'code_barre' => $data['code_barre'] ?: null,
            'cote_dewey' => $data['cote_dewey'] ?: null,
            'category_id' => $category?->id,
            'couverture_couleur' => $data['couverture_couleur'] ?: null,
            'rayon' => $data['rayon'] ?: null,
            'mots_cles' => $data['mots_cles'] ?: [],
            'chapitres' => $data['chapitres'] ?: [],
            'cover_image' => $catalogData['cover_image'] ?? null,
            'statut' => 'a_verifier',
            'ai_raw_response' => [
                'catalog_source' => $catalogResult['source'] ?? null,
                'catalog' => $catalogResult['raw'] ?? null,
            ],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'book' => $book,
                'redirect' => route('books.edit', $book),
                'source' => $catalogResult['source'],
            ]);
        }

        return redirect()
            ->route('books.edit', $book)
            ->with('success', 'Livre trouvé dans '.$catalogResult['source'].'. Merci de vérifier les informations avant de valider.');
    }

    protected function hasValidIsbnChecksum(string $isbn): bool
    {
        if (strlen($isbn) === 10) {
            $sum = 0;
            foreach (str_split($isbn) as $index => $character) {
                $value = $character === 'X' ? 10 : (int) $character;
                $sum += $value * (10 - $index);
            }

            return $sum % 11 === 0;
        }

        $sum = 0;
        for ($index = 0; $index < 12; $index++) {
            $sum += (int) $isbn[$index] * ($index % 2 === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10 === (int) $isbn[12];
    }
}
