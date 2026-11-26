@extends('layouts.app')

@section('title', 'BookManager - Catalogue')

@section('content')

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs font-semibold text-secondary uppercase tracking-wider mb-1">
            <i data-lucide="library" class="w-4 h-4" aria-hidden="true"></i>
            <span>Inventaire numérisé</span>
        </div>
        <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-on-surface">Catalogue</h2>
        <p class="font-body-md text-sm sm:text-base text-on-surface-variant mt-1">Gérez, filtrez, ajouter et modifier vos fiches d'ouvrages.</p>
    </div>
    <div class="flex items-center gap-3 self-start md:self-auto">
        <a href="{{ route('scan.index') }}" class="px-4 py-2 bg-primary text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-primary-container transition-all shadow-xs flex items-center gap-2 hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
            <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
            <span>Nouveau livre</span>
        </a>
        {{-- <div class="flex bg-surface-container-lowest rounded-xl border border-outline-variant shadow-2xs overflow-hidden">
            <a href="{{ route('books.export.pdf', request()->query()) }}" class="px-3.5 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors flex items-center gap-1.5 border-r border-outline-variant">
                <i data-lucide="file-text" class="w-4 h-4 text-rose-600" aria-hidden="true"></i>
                <span>PDF</span>
            </a>
            <a href="{{ route('books.export.excel', request()->query()) }}" class="px-3.5 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors flex items-center gap-1.5">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600" aria-hidden="true"></i>
                <span>Excel</span>
            </a>
        </div> --}}
    </div>
</div>

<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden shadow-xs">
    {{-- Barre d'outils / Filtres --}}
    <form action="{{ route('books.index') }}" method="GET" class="p-4 sm:p-5 border-b border-outline-variant/60 flex flex-col sm:flex-row justify-between gap-3 sm:gap-4 items-stretch sm:items-center bg-surface-bright">
        <div class="flex items-center gap-3 flex-wrap">
            <select name="statut" onchange="this.form.submit()" class="rounded-xl border border-outline-variant bg-white pl-3 pr-8 py-2 text-xs font-medium text-on-surface focus:ring-2 focus:ring-secondary/30 focus:outline-none">
                <option value="">Tous les statuts</option>
                <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                <option value="a_verifier" @selected(request('statut') === 'a_verifier')>À vérifier</option>
            </select>
            <select name="category_id" onchange="this.form.submit()" class="bg-surface-container-lowest border border-outline-variant rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                <option value="">Toutes les rubriques</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->nom }}</option>
                @endforeach
            </select>
            @if(request('q') || request('statut') || request('category_id'))
                <a href="{{ route('books.index') }}" class="text-xs text-secondary hover:underline font-semibold flex items-center gap-1">
                    <i data-lucide="x" class="w-3.5 h-3.5" aria-hidden="true"></i>
                    <span>Réinitialiser</span>
                </a>
            @endif
        </div>

        <div class="relative w-full sm:max-w-xs">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none">
                <i data-lucide="search" class="w-4 h-4" aria-hidden="true"></i>
            </span>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par titre, ISBN..."
                class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
            >
        </div>
    </form>

    {{-- Table du catalogue --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[850px]">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/60 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">
                    <th class="px-6 py-3.5 w-20">Couverture</th>
                    <th class="px-6 py-3.5 w-32">Cote (Dewey)</th>
                    <th class="px-6 py-3.5">Rubrique</th>
                    <th class="px-6 py-3.5">Titre</th>
                    <th class="px-6 py-3.5">Auteur</th>
                    <th class="px-6 py-3.5">ISBN</th>
                    <th class="px-6 py-3.5 w-24 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40 bg-surface-container-lowest">
                @forelse ($books as $book)
                    <tr class="hover:bg-surface-dim/40 transition-colors group">
                        <td class="px-6 py-3.5">
                            <div class="w-11 h-14 bg-surface-container rounded-lg border border-outline-variant overflow-hidden flex items-center justify-center text-outline shadow-2xs group-hover:scale-105 transition-transform duration-200">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="{{ $book->titre }}">
                                @else
                                    <i data-lucide="book" class="w-5 h-5 text-outline" aria-hidden="true"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3.5 font-mono text-xs font-bold text-primary">
                            @if ($book->cote_dewey)
                                <span class="px-2 py-0.5 rounded-md bg-surface-container border border-outline-variant/60">{{ $book->cote_dewey }}</span>
                            @else
                                <span class="text-outline font-normal">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-xs">
                            @if ($book->category)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $book->category->couleur }}18; color: {{ $book->category->couleur }}">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $book->category->couleur }}"></span>
                                    {{ $book->category->nom }}
                                </span>
                            @else
                                <span class="text-outline text-xs">Sans rubrique</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-sm font-semibold text-on-surface">
                            <a href="{{ route('books.edit', $book) }}" class="group-hover:text-secondary transition-colors block">
                                {{ $book->titre }}
                            </a>
                        </td>
                        <td class="max-w-xs whitespace-normal break-words px-6 py-3.5 text-xs text-on-surface-variant font-medium">
                            {{ $book->auteur ?: '—' }}
                        </td>
                        <td class="px-6 py-3.5 text-xs font-mono text-on-surface-variant">
                            {{ $book->isbn ?: '—' }}
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('books.edit', $book) }}" class="p-2 rounded-xl text-outline hover:text-primary hover:bg-surface-container transition-colors" title="Modifier" aria-label="Modifier l'ouvrage {{ $book->titre }}">
                                    <i data-lucide="pencil" class="w-4 h-4" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Confirmer la suppression de cet ouvrage ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-outline hover:text-error hover:bg-error-container/40 transition-colors" title="Supprimer" aria-label="Supprimer l'ouvrage {{ $book->titre }}">
                                        <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-on-surface-variant text-sm">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-surface-container text-outline flex items-center justify-center mx-auto">
                                    <i data-lucide="search-x" class="w-6 h-6" aria-hidden="true"></i>
                                </div>
                                <p class="font-medium text-on-surface">Aucun livre ne correspond à votre recherche.</p>
                                <a href="{{ route('books.index') }}" class="text-secondary text-xs font-semibold hover:underline">Réinitialiser les filtres</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($books->hasPages())
        <div class="p-4 sm:p-5 border-t border-outline-variant/60">
            {{ $books->links() }}
        </div>
    @endif
</div>

@endsection
