@extends('layouts.app')

@section('title', 'BookManager - Aperçu de l’export Excel')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-secondary hover:text-primary mb-2 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                <span>Retour au catalogue</span>
            </a>
            <h2 class="font-headline-md text-2xl sm:text-3xl font-bold text-on-surface">Exportation PDF/Excel</h2>
            <p class="font-body-md text-xs sm:text-sm text-on-surface-variant mt-1">
                Visualisez les {{ $books->total() }} livre(s) sous la même disposition que la feuille de calcul générée.
            </p>
        </div>
        <div class="flex bg-surface-container-lowest rounded-xl border border-outline-variant shadow-2xs overflow-hidden">
            <a href="{{ route('books.export.pdf', request()->query()) }}" class="px-3.5 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors flex items-center gap-1.5 border-r border-outline-variant">
                <i data-lucide="file-text" class="w-4 h-4 text-rose-600" aria-hidden="true"></i>
                <span>PDF</span>
            </a>
            <a href="{{ route('books.export.excel', request()->query()) }}" class="px-3.5 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors flex items-center gap-1.5">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600" aria-hidden="true"></i>
                <span>Excel</span>
            </a>
        </div>
        {{-- <a href="{{ route('books.export.excel', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 self-start rounded-xl bg-primary px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-xs transition-all hover:bg-primary-container hover:shadow hover:-translate-y-0.5 active:translate-y-0">
            <i data-lucide="download" class="w-4 h-4" aria-hidden="true"></i>
            <span>Télécharger</span>
        </a> --}}
    </div>

    <section class="overflow-hidden rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-xs">
        <form action="{{ route('books.export.index') }}" method="GET" class="flex flex-col gap-4 border-b border-outline-variant bg-surface-bright p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2.5">
                <select name="statut" class="rounded-xl border border-outline-variant bg-white pl-3 pr-8 py-2 text-xs font-medium text-on-surface focus:ring-2 focus:ring-secondary/30 focus:outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                    <option value="a_verifier" @selected(request('statut') === 'a_verifier')>À vérifier</option>
                </select>
                <select name="category_id" class="rounded-xl border border-outline-variant bg-white pl-3 pr-8 py-2 text-xs font-medium text-on-surface focus:ring-2 focus:ring-secondary/30 focus:outline-none">
                    <option value="">Toutes les rubriques</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->nom }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl border border-outline-variant bg-white px-3.5 py-2 text-xs font-semibold text-primary transition-colors hover:bg-surface-container shadow-2xs">
                    Filtrer
                </button>
                @if (request()->hasAny(['q', 'statut', 'category_id']))
                    <a href="{{ route('books.export.index') }}" class="px-2 py-2 text-xs text-on-surface-variant hover:text-primary font-medium">Effacer</a>
                @endif
            </div>

            <div class="flex w-full gap-2 lg:max-w-md">
                <label for="export-search" class="sr-only">Rechercher dans le catalogue</label>
                <input id="export-search" type="search" name="q" value="{{ request('q') }}"
                       placeholder="Titre, auteur, ISBN, cote..."
                       class="min-w-0 flex-1 rounded-xl border border-outline-variant bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary shadow-2xs">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-surface-container px-3.5 py-2 text-xs font-semibold text-primary transition-colors hover:bg-surface-dim">
                    <i data-lucide="search" class="w-4 h-4" aria-hidden="true"></i>
                    <span>Rechercher</span>
                </button>
            </div>
        </form>

        <div class="flex flex-col gap-1 border-b border-outline-variant px-5 py-3 sm:flex-row sm:items-center sm:justify-between bg-surface-container-low/40">
            <p class="text-xs font-semibold text-on-surface">
                {{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }} sur {{ $books->total() }} livre(s)
            </p>
            <p class="text-[11px] text-on-surface-variant">Les colonnes correspondent exactement au classeur Excel généré.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1400px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-outline-variant/60 bg-surface-container-low text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">
                        @foreach ($headings as $heading)
                            <th scope="col" class="whitespace-nowrap px-4 py-3">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 bg-surface-container-lowest">
                    @forelse ($rows as $row)
                        <tr class="transition-colors hover:bg-surface-dim/30">
                            @foreach ($row as $value)
                                <td class="max-w-xs whitespace-normal break-words px-4 py-2.5 text-xs text-on-surface">
                                    @if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                                        <a href="{{ $value }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover:underline font-mono text-[11px]">{{ $value }}</a>
                                    @else
                                        {{ filled($value) ? $value : '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headings) }}" class="px-6 py-12 text-center text-on-surface-variant text-sm">
                                Aucun livre ne correspond à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($books->hasPages())
            <div class="border-t border-outline-variant/60 p-4 sm:p-5">
                {{ $books->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
