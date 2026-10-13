@extends('layouts.app')

@section('title', 'BookManager - Catalogue')

@section('content')

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Catalogue</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Gérez, exportez et téléchargez vos inventaires numérisés.</p>
    </div>
    <div class="flex items-center gap-3 self-start md:self-auto">
        <div class="flex bg-surface-container-lowest rounded-lg border border-outline-variant shadow-sm overflow-hidden">
            <a href="{{ route('books.export.pdf', request()->query()) }}" class="px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-2 border-r border-outline-variant">
                <span class="material-symbols-outlined text-outline text-[20px]">picture_as_pdf</span> PDF
            </a>
            <a href="{{ route('books.export.index', request()->query()) }}" class="px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-outline text-[20px]">table</span> Excel
            </a>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
    {{-- Barre d'outils --}}
    <form action="{{ route('books.index') }}" method="GET" class="p-lg border-b border-outline-variant flex flex-col sm:flex-row justify-between gap-md items-stretch sm:items-center bg-surface-bright">
        <div class="flex items-center gap-md flex-wrap">
            <select name="statut" onchange="this.form.submit()" class="bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-2 text-sm">
                <option value="">Tous les statuts</option>
                <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                <option value="a_verifier" @selected(request('statut') === 'a_verifier')>À vérifier</option>
            </select>
            <select name="category_id" onchange="this.form.submit()" class="bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-2 text-sm">
                <option value="">Toutes les rubriques</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative w-full sm:max-w-xs">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un livre..."
                   class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-shadow">
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant text-xs font-semibold text-on-surface-variant uppercase tracking-wider">
                    <th class="px-6 py-4">Couverture</th>
                    <th class="px-6 py-4">Cote (Dewey)</th>
                    <th class="px-6 py-4">Rubrique</th>
                    <th class="px-6 py-4">Titre</th>
                    <th class="px-6 py-4">Auteur</th>
                    <th class="px-6 py-4">Éditeur</th>
                    <th class="px-6 py-4">ISBN</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
                @forelse ($books as $book)
                    <tr class="hover:bg-surface-dim/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="w-10 h-14 bg-surface-container rounded border border-outline-variant overflow-hidden flex items-center justify-center text-outline">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <span class="material-symbols-outlined text-[18px]">broken_image</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">{{ $book->cote_dewey ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $book->category?->nom ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-on-surface">{{ $book->titre }}</td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $book->auteur ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $book->editeur ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-on-surface-variant">{{ $book->isbn ?: '—' }}</td>
                        {{-- <td class="px-6 py-4">
                            @if ($book->statut === 'valide')
                                <span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-secondary-container/20 text-on-secondary-fixed font-label-sm text-label-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Validé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-error-container/40 text-on-error-container font-label-sm text-label-sm border border-error-container">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error"></span> À vérifier
                                </span>
                            @endif
                        </td> --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('books.edit', $book) }}" class="text-outline hover:text-primary" aria-label="Modifier">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Supprimer ce livre ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-outline hover:text-error" aria-label="Supprimer">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-xl text-center text-on-surface-variant">Aucun livre ne correspond à votre recherche.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-lg border-t border-outline-variant">
        {{ $books->links() }}
    </div>
</div>

@endsection
