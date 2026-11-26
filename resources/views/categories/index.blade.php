@extends('layouts.app')

@section('title', 'BookManager - Rubriques')

@section('content')

<div class="space-y-6 sm:space-y-8">
    <div>
        <div class="flex items-center gap-2 text-xs font-semibold text-secondary uppercase tracking-wider mb-1">
            <i data-lucide="folder-tree" class="w-4 h-4" aria-hidden="true"></i>
            <span>Classification &amp; Taxonomie</span>
        </div>
        <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-on-surface">Rubriques (Classification Dewey)</h2>
        <p class="font-body-md text-sm sm:text-base text-on-surface-variant mt-1">Gérez les grandes rubriques utilisées pour classer vos ouvrages et structurer votre bibliothèque.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Formulaire d'ajout -->
        <div class="lg:col-span-5 bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-headline-md text-base sm:text-lg text-on-surface font-bold">Ajouter une rubrique</h3>
                <p class="text-xs text-on-surface-variant mt-1">Créez une catégorie avec une couleur distinctive.</p>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="nom" class="block text-xs sm:text-sm font-semibold text-on-surface">Nom de la rubrique</label>
                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        placeholder="Ex: Sciences, Histoire, Roman..."
                        required
                        class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-on-surface focus:ring-2 focus:ring-secondary/30 focus:outline-none focus:border-secondary transition-all shadow-2xs"
                    >
                </div>

                <div class="space-y-1">
                    <label for="couleur" class="block text-xs sm:text-sm font-semibold text-on-surface">Couleur d'identification</label>
                    <div class="flex items-center gap-3">
                        <input
                            type="color"
                            name="couleur"
                            id="couleur"
                            value="#0058be"
                            class="w-14 h-10 border border-outline-variant rounded-xl cursor-pointer bg-white p-1"
                        >
                        <span class="text-xs text-on-surface-variant">Cette couleur sera utilisée sur les badges et les graphiques de répartition.</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary-container text-white py-2.5 px-4 rounded-xl font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
                    <span>Créer la rubrique</span>
                </button>
            </form>
        </div>

        <!-- Liste des catégories existantes -->
        <div class="lg:col-span-7 bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-outline-variant/60 pb-3">
                <div>
                    <h3 class="font-headline-md text-base sm:text-lg text-on-surface font-bold">Rubriques enregistrées</h3>
                    <p class="text-xs text-on-surface-variant">Classées par ordre alphabétique</p>
                </div>
                <span class="px-2.5 py-1 bg-surface-container text-primary font-bold text-xs rounded-full">
                    {{ $categories->count() }} rubrique{{ $categories->count() > 1 ? 's' : '' }}
                </span>
            </div>

            <div class="divide-y divide-outline-variant/40">
                @forelse ($categories as $cat)
                    <div class="gsap-batch-item flex items-center justify-between py-3 hover:bg-surface-dim/30 px-2 rounded-xl transition-colors group">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-3.5 h-3.5 rounded-md flex-shrink-0 shadow-2xs" style="background-color: {{ $cat->couleur ?? '#0058be' }}"></span>
                            <div class="min-w-0">
                                <span class="font-semibold text-xs sm:text-sm text-on-surface truncate block">{{ $cat->nom }}</span>
                                <span class="text-[11px] text-on-surface-variant font-mono">
                                    {{ $cat->books_count }} ouvrage{{ $cat->books_count > 1 ? 's' : '' }} associé{{ $cat->books_count > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Voulez-vous supprimer cette rubrique ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl text-outline hover:text-error hover:bg-error-container/40 transition-colors opacity-70 group-hover:opacity-100" title="Supprimer" aria-label="Supprimer la rubrique {{ $cat->nom }}">
                                <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-12 text-center text-on-surface-variant text-sm">
                        Aucune rubrique enregistrée pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
