<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #111c2d; }
        h1 { color: #00236f; font-size: 20px; margin-bottom: 4px; }
        p.subtitle { color: #444651; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #e7eeff; color: #444651; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        td { padding: 6px 8px; border-bottom: 1px solid #d8e3fb; }
        tr:nth-child(even) td { background-color: #f9f9ff; }
        .badge { padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .badge-valide { background-color: #d8e3fb; color: #00236f; }
        .badge-verif { background-color: #ffdad6; color: #93000a; }
    </style>
</head>
<body>
    <h1>Catalogue BookManager</h1>
    <p class="subtitle">Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $books->count() }} livre(s)</p>

    <table>
        <thead>
            <tr>
                <th>Cote</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Éditeur</th>
                <th>Année</th>
                <th>ISBN</th>
                <th>Rubrique</th>
                <th>Rayon</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $book)
                <tr>
                    <td>{{ $book->cote_dewey }}</td>
                    <td>{{ $book->titre }}</td>
                    <td>{{ $book->auteur }}</td>
                    <td>{{ $book->editeur }}</td>
                    <td>{{ $book->date_edition }}</td>
                    <td>{{ $book->isbn }}</td>
                    <td>{{ $book->category?->nom }}</td>
                    <td>{{ $book->rayon }}</td>
                    {{-- <td>
                        <span class="badge {{ $book->statut === 'valide' ? 'badge-valide' : 'badge-verif' }}">
                            {{ $book->statut === 'valide' ? 'Validé' : 'À vérifier' }}
                        </span>
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
