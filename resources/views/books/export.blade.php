@extends('layouts.app')

@section('title', 'BookManager - Aperçu de l’export Excel')

@section('content')
<div class="flex flex-col gap-lg">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-md">
        <div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-xs text-sm font-medium text-primary hover:underline mb-sm">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Retour au catalogue
            </a>
            <h2 class="font-headline-md text-headline-md text-on-surface">Aperçu de l’export Excel</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Consultez les {{ $books->total() }} livre(s) du catalogue dans le format du fichier à télécharger.
            </p>
        </div>
        <a href="{{ route('books.export.excel', request()->query()) }}"
           class="inline-flex items-center justify-center gap-sm self-start rounded-lg bg-primary px-lg py-md font-label-md text-label-md text-on-primary shadow-sm transition-colors hover:bg-primary-container">
            <span class="material-symbols-outlined">download</span>
            Télécharger le fichier Excel
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
        <form action="{{ route('books.export.index') }}" method="GET" class="flex flex-col gap-md border-b border-outline-variant bg-surface-bright p-lg lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-sm">
                <select name="statut" class="rounded-lg border border-outline-variant bg-surface-container-lowest px-md py-2 text-sm">
                    <option value="">Tous les statuts</option>
                    <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                    <option value="a_verifier" @selected(request('statut') === 'a_verifier')>À vérifier</option>
                </select>
                <select name="category_id" class="rounded-lg border border-outline-variant bg-surface-container-lowest px-md py-2 text-sm">
                    <option value="">Toutes les rubriques</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->nom }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg border border-outline-variant px-md py-2 text-sm font-medium text-primary transition-colors hover:bg-surface-container-low">
                    Filtrer
                </button>
                @if (request()->hasAny(['q', 'statut', 'category_id']))
                    <a href="{{ route('books.export.index') }}" class="px-sm py-2 text-sm text-on-surface-variant hover:text-primary">Effacer</a>
                @endif
            </div>

            <div class="flex w-full gap-sm lg:max-w-md">
                <label for="export-search" class="sr-only">Rechercher dans le catalogue</label>
                <input id="export-search" type="search" name="q" value="{{ request('q') }}"
                       placeholder="Titre, auteur, ISBN, cote ou code-barres..."
                       class="min-w-0 flex-1 rounded-lg border border-outline-variant bg-surface-container-lowest px-md py-2 text-sm focus:outline-none focus:ring-2 focus:ring-secondary">
                <button type="submit" class="inline-flex items-center gap-xs rounded-lg bg-surface-container px-md py-2 text-sm font-medium text-primary transition-colors hover:bg-surface-dim">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                    Rechercher
                </button>
            </div>
        </form>

        <div class="flex flex-col gap-xs border-b border-outline-variant px-lg py-md sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-on-surface-variant">
                {{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }} sur {{ $books->total() }} livre(s)
            </p>
            <p class="text-xs text-on-surface-variant">Les colonnes correspondent au fichier Excel téléchargé.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1500px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                        @foreach ($headings as $heading)
                            <th scope="col" class="whitespace-nowrap px-md py-md">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50">
                    @forelse ($rows as $row)
                        <tr class="transition-colors hover:bg-surface-container-low/70">
                            @foreach ($row as $value)
                                <td class="max-w-xs whitespace-nowrap px-md py-sm text-sm text-on-surface">
                                    @if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                                        <a href="{{ $value }}" target="_blank" rel="noopener noreferrer" class="text-primary underline">{{ $value }}</a>
                                    @else
                                        {{ filled($value) ? $value : '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headings) }}" class="px-lg py-xl text-center text-on-surface-variant">
                                Aucun livre ne correspond à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($books->hasPages())
            <div class="border-t border-outline-variant p-lg">
                {{ $books->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
