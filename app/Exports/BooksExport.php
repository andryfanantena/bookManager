<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    private int $mappedBooks = 0;

    public function __construct(
        protected Collection $books,
        private int $startingIndex = 0,
    ) {}

    public function collection(): Collection
    {
        return $this->books;
    }

    public function headings(): array
    {
        return [
            'N°',
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

    public function columnWidths(): array
    {
        return ['O' => 35];
    }

    public function styles(Worksheet $sheet): array
    {
        return array_fill_keys(range('A', 'O'), [
            'alignment' => [
                'vertical' => 'top',
                'wrapText' => true,
            ],
        ]);
    }

    /** @param Book $book */
    public function map(mixed $book): array
    {
        return [
            $this->startingIndex + ++$this->mappedBooks,
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
            collect($book->chapitres ?? [])
                ->filter(fn ($chapter) => is_string($chapter) && trim($chapter) !== '')
                ->implode(', '),
        ];
    }
}
