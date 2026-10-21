@extends('layouts.app')

@section('title', 'BookManager - Modification')

@section('content')
<div x-data="chapterEditor({{ Js::from($book->chapitres ?? []) }})">

    <header class="flex items-center justify-between mb-lg">
        <div class="flex items-center gap-md">
            <a href="{{ route('books.index') }}" class="text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full p-2 flex items-center justify-center">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="font-headline-md text-headline-md font-bold text-primary">Modification</div>
        </div>
        @if ($book->statut === 'a_verifier')
            <span class="font-label-md text-label-md text-error flex items-center gap-xs">
                <span class="material-symbols-outlined">info</span> Vérification requise
            </span>
        @else
            <span class="font-label-md text-label-md text-secondary flex items-center gap-xs">
                <span class="material-symbols-outlined">check_circle</span> Validé
            </span>
        @endif
    </header>

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data" class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-lg">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-12 gap-xl">
            {{-- Colonne gauche : couverture --}}
            <div class="md:col-span-4 lg:col-span-3 flex flex-col gap-md">
                <label class="aspect-[2/3] w-full rounded-lg border border-outline-variant overflow-hidden bg-surface-container flex items-center justify-center relative group cursor-pointer">
                    @if ($book->cover_url)
                        <img id="cover-preview" src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="Couverture" onerror="coverImageFailed(this)">
                    @else
                        <img id="cover-preview" src="" class="w-full h-full object-cover hidden" alt="Couverture">
                    @endif
                    <span id="cover-placeholder" class="material-symbols-outlined text-[48px] text-outline{{ $book->cover_url ? ' hidden' : '' }}">image</span>
                    <div class="absolute inset-0 bg-on-background/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                        <span class="bg-surface text-primary font-label-md text-label-md px-md py-sm rounded-full flex items-center gap-xs shadow-md">
                            <span class="material-symbols-outlined">edit</span> Modifier l'image
                        </span>
                    </div>
                    <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewCover(event)">
                </label>
            </div>

            {{-- Colonne droite : formulaire --}}
            <div class="md:col-span-8 lg:col-span-9 flex flex-col gap-xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Cote (Dewey)</label>
                        <input name="cote_dewey" type="text" value="{{ old('cote_dewey', $book->cote_dewey) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm font-mono focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Rubrique</label>
                        <select name="category_id" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                            <option value="">— Aucune —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $book->category_id) == $cat->id)>{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Nombre d'exemplaires</label>
                        <input name="nombre_exemplaires" type="number" min="0" value="{{ old('nombre_exemplaires', $book->nombre_exemplaires ?? 1) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Auteur</label>
                        <input name="auteur" type="text" value="{{ old('auteur', $book->auteur) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Éditeur</label>
                        <input name="editeur" type="text" value="{{ old('editeur', $book->editeur) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Date d'édition</label>
                        <input name="date_edition" type="text" value="{{ old('date_edition', $book->date_edition) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Code-barres</label>
                        <input name="code_barre" type="text" value="{{ old('code_barre', $book->code_barre) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm font-mono focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">ISBN</label>
                        <input name="isbn" type="text" value="{{ old('isbn', $book->isbn) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm font-mono focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1 md:col-span-3">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Titre</label>
                        <input name="titre" type="text" required value="{{ old('titre', $book->titre) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Couleur de couverture</label>
                        <input name="couverture_couleur" type="text" value="{{ old('couverture_couleur', $book->couverture_couleur) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Lien d'éditeur</label>
                        <input name="lien_editeur" type="url" value="{{ old('lien_editeur', $book->lien_editeur) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block font-label-md text-label-md text-on-surface mb-xs">Rayon</label>
                        <input name="rayon" type="text" value="{{ old('rayon', $book->rayon) }}"
                               class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-sm focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="w-full h-px bg-outline-variant my-sm"></div>

                {{-- Chapitres dynamiques --}}
                <div>
                    <div class="flex justify-between items-center mb-md">
                        <h3 class="font-headline-md text-headline-md font-semibold text-on-surface">Chapitres / Parties essentielles</h3>
                        <button type="button" @click="add()" class="text-secondary font-label-md text-label-md flex items-center gap-xs hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">add</span> Ajouter un chapitre
                        </button>
                    </div>
                    <div class="flex flex-col gap-sm">
                        <template x-for="(chapitre, index) in chapitres" :key="index">
                            <div class="flex items-center gap-md bg-surface border border-outline-variant rounded-lg p-sm group hover:border-secondary transition-colors">
                                <span class="material-symbols-outlined text-outline-variant cursor-grab">drag_indicator</span>
                                <input type="text" :name="'chapitres[' + index + ']'" x-model="chapitres[index]" placeholder="Titre du chapitre"
                                       class="flex-grow bg-transparent border-none focus:ring-0 p-0">
                                <button type="button" @click="remove(index)" class="text-on-surface-variant hover:text-error transition-colors opacity-0 group-hover:opacity-100 p-1">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </template>
                        <p x-show="chapitres.length === 0" class="text-on-surface-variant text-label-sm">Aucun chapitre renseigné.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-xl pt-lg border-t border-outline-variant flex justify-end items-center gap-md">
            <a href="{{ route('books.index') }}" class="font-label-md text-label-md text-secondary px-lg py-sm rounded-lg hover:bg-surface-container-low transition-colors">
                Annuler
            </a>
            <button type="submit" name="action" value="save_draft"
                    class="font-label-md text-label-md text-primary border border-outline-variant px-lg py-sm rounded-lg hover:bg-surface-dim transition-colors">
                Enregistrer sans valider
            </button>
            <button type="submit" name="action" value="validate"
                    class="bg-[#3B82F6] text-white font-label-md text-label-md px-lg py-sm rounded-lg hover:bg-primary-container transition-colors shadow-sm flex items-center gap-xs">
                <span class="material-symbols-outlined">check_circle</span> Enregistrer et valider
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function chapterEditor(initial) {
        return {
            chapitres: initial && initial.length ? initial : [],
            add() { this.chapitres.push(''); },
            remove(index) { this.chapitres.splice(index, 1); },
        };
    }

    function previewCover(event) {
        const file = event.target.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        const img = document.getElementById('cover-preview');
        const placeholder = document.getElementById('cover-placeholder');
        img.src = url;
        img.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    }

    function coverImageFailed(image) {
        image.classList.add('hidden');
        document.getElementById('cover-placeholder')?.classList.remove('hidden');
    }
</script>
@endpush

@endsection
