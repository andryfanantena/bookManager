<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $books)
    {
    }

    public function collection(): Collection
    {
        return $this->books;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cote (Dewey)',
            'Rubrique',
            'Titre',
            'Auteur',
            'Éditeur',
            "Date d'édition",
            'ISBN',
            'Code barre',
            "Nombre d'exemplaires",
            'Couleur de couverture',
            'Lien éditeur',
            'Rayon',
            'Statut',
            'Mots clés',
        ];
    }

    /** @param Book $book */
    public function map(mixed $book): array
    {
        return [
            $book->id,
            $book->cote_dewey,
            $book->category?->nom,
            $book->titre,
            $book->auteur,
            $book->editeur,
            $book->date_edition,
            $book->isbn,
            $book->code_barre,
            $book->nombre_exemplaires,
            $book->couverture_couleur,
            $book->lien_editeur,
            $book->rayon,
            $book->statut === 'valide' ? 'Validé' : 'À vérifier',
            $book->mots_cles ? implode(', ', $book->mots_cles) : '',
        ];
    }
}
