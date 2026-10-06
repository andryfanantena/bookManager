@extends('layouts.app')

@section('title', 'BookManager - Catégories')

@section('content')

<div>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Rubriques (catégories Dewey)</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Gérez les grandes rubriques utilisées pour classer vos livres.</p>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg">
    <form action="{{ route('categories.store') }}" method="POST" class="flex flex-col sm:flex-row gap-md mb-lg">
        @csrf
        <input type="text" name="nom" placeholder="Nom de la rubrique (ex: Sciences)" required
               class="flex-1 bg-surface border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
        <input type="color" name="couleur" value="#0058be" class="w-14 h-11 border border-outline-variant rounded-lg cursor-pointer">
        <button type="submit" class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-xs">
            <span class="material-symbols-outlined text-[20px]">add</span> Ajouter
        </button>
    </form>

    <div class="divide-y divide-outline-variant/30">
        @forelse ($categories as $cat)
            <div class="flex items-center justify-between py-md">
                <div class="flex items-center gap-md">
                    <span class="w-4 h-4 rounded-sm" style="background-color: {{ $cat->couleur ?? '#0058be' }}"></span>
                    <span class="font-body-md text-body-md text-on-surface font-medium">{{ $cat->nom }}</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">({{ $cat->books_count }} livre{{ $cat->books_count > 1 ? 's' : '' }})</span>
                </div>
                <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Supprimer cette rubrique ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-outline hover:text-error transition-colors" aria-label="Supprimer">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                </form>
            </div>
        @empty
            <p class="text-on-surface-variant font-body-md text-body-md text-center py-lg">Aucune rubrique pour le moment.</p>
        @endforelse
    </div>
</div>

@endsection
