@extends('layouts.app')

@section('title', 'BookManager - Modification')

@section('content')
<div x-data="chapterEditor({{ Js::from($book->chapitres ?? []) }})" class="space-y-6">

    <header class="flex items-center justify-between pb-4 border-b border-outline-variant/60">
        <div class="flex items-center gap-3">
            <a href="{{ route('books.index') }}" aria-label="Retour au catalogue" class="text-on-surface-variant hover:bg-surface-container transition-colors rounded-xl p-2 flex items-center justify-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                <i data-lucide="arrow-left" class="w-5 h-5" aria-hidden="true"></i>
            </a>
            <div>
                <h2 class="font-headline-md text-xl sm:text-2xl font-bold text-on-surface">Modifier la fiche</h2>
                <p class="text-xs text-on-surface-variant font-mono">{{ $book->titre }}</p>
            </div>
        </div>
        @if ($book->statut === 'a_verifier')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                <i data-lucide="alert-triangle" class="w-4 h-4" aria-hidden="true"></i>
                <span>Vérification requise</span>
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                <i data-lucide="check-circle-2" class="w-4 h-4" aria-hidden="true"></i>
                <span>Validé</span>
            </span>
        @endif
    </header>

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data" class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-xs p-5 sm:p-7">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8">
            {{-- Colonne gauche : couverture --}}
            <div class="md:col-span-4 lg:col-span-3 flex flex-col gap-3">
                <span class="block text-xs font-semibold text-on-surface">Image de couverture</span>
                <label class="aspect-[2/3] w-full rounded-2xl border-2 border-dashed border-outline-variant overflow-hidden bg-surface-container flex items-center justify-center relative group cursor-pointer shadow-2xs hover:border-secondary transition-all">
                    @if ($book->cover_url)
                        <img id="cover-preview" src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="Couverture" onerror="coverImageFailed(this)">
                    @else
                        <img id="cover-preview" src="" class="w-full h-full object-cover hidden" alt="Couverture">
                    @endif
                    <div id="cover-placeholder" class="flex flex-col items-center justify-center text-outline gap-2 p-4 text-center {{ $book->cover_url ? 'hidden' : '' }}">
                        <i data-lucide="image" class="w-10 h-10 text-outline" aria-hidden="true"></i>
                        <span class="text-xs font-medium">Ajouter une image</span>
                    </div>
                    <div class="absolute inset-0 bg-primary/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-xs">
                        <span class="bg-white text-primary text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-md">
                            <i data-lucide="pencil" class="w-3.5 h-3.5" aria-hidden="true"></i>
                            <span>Modifier</span>
                        </span>
                    </div>
                    <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewCover(event)">
                </label>
                <p class="text-[11px] text-on-surface-variant text-center">Cliquez pour importer un fichier image (JPG, PNG).</p>
            </div>

            {{-- Colonne droite : formulaire --}}
            <div class="md:col-span-8 lg:col-span-9 flex flex-col gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    <div class="col-span-1">
                        <label for="cote_dewey" class="block text-xs font-semibold text-on-surface mb-1">Cote (Dewey)</label>
                        <input id="cote_dewey" name="cote_dewey" type="text" value="{{ old('cote_dewey', $book->cote_dewey) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs font-mono text-primary font-bold focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="category_id" class="block text-xs font-semibold text-on-surface mb-1">Rubrique</label>
                        <select id="category_id" name="category_id" class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                            <option value="">— Aucune —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $book->category_id) == $cat->id)>{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label for="nombre_exemplaires" class="block text-xs font-semibold text-on-surface mb-1">Nombre d'exemplaires</label>
                        <input id="nombre_exemplaires" name="nombre_exemplaires" type="number" min="0" value="{{ old('nombre_exemplaires', $book->nombre_exemplaires ?? 1) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="auteur" class="block text-xs font-semibold text-on-surface mb-1">Auteur</label>
                        <textarea id="auteur" name="auteur" rows="2"
                                  class="w-full resize-y whitespace-pre-wrap break-words bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">{{ old('auteur', $book->auteur) }}</textarea>
                    </div>

                    <div class="col-span-1">
                        <label for="editeur" class="block text-xs font-semibold text-on-surface mb-1">Éditeur</label>
                        <input id="editeur" name="editeur" type="text" value="{{ old('editeur', $book->editeur) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="date_edition" class="block text-xs font-semibold text-on-surface mb-1">Date d'édition</label>
                        <input id="date_edition" name="date_edition" type="text" value="{{ old('date_edition', $book->date_edition) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="code_barre" class="block text-xs font-semibold text-on-surface mb-1">Code-barres</label>
                        <input id="code_barre" name="code_barre" type="text" value="{{ old('code_barre', $book->code_barre) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 font-mono text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="isbn" class="block text-xs font-semibold text-on-surface mb-1">ISBN</label>
                        <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $book->isbn) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 font-mono text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="rayon" class="block text-xs font-semibold text-on-surface mb-1">Rayon</label>
                        <input id="rayon" name="rayon" type="text" value="{{ old('rayon', $book->rayon) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1 sm:col-span-2 md:col-span-3">
                        <label for="titre" class="block text-xs font-semibold text-on-surface mb-1">Titre de l'ouvrage</label>
                        <input id="titre" name="titre" type="text" required value="{{ old('titre', $book->titre) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-sm font-semibold focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1">
                        <label for="couverture_couleur" class="block text-xs font-semibold text-on-surface mb-1">Couleur de couverture</label>
                        <input id="couverture_couleur" name="couverture_couleur" type="text" value="{{ old('couverture_couleur', $book->couverture_couleur) }}"
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>

                    <div class="col-span-1 sm:col-span-2">
                        <label for="lien_editeur" class="block text-xs font-semibold text-on-surface mb-1">Lien d'éditeur / fiche web</label>
                        <input id="lien_editeur" name="lien_editeur" type="url" value="{{ old('lien_editeur', $book->lien_editeur) }}"
                               placeholder="https://..."
                               class="w-full bg-white border border-outline-variant rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-secondary/30 focus:border-secondary focus:outline-none shadow-2xs">
                    </div>
                </div>

                <div class="w-full h-px bg-outline-variant/60 my-2"></div>

                {{-- Chapitres dynamiques --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="font-headline-md text-sm sm:text-base font-bold text-on-surface">Chapitres / Sommaire</h3>
                        <button type="button" @click="add()" class="text-secondary font-semibold text-xs flex items-center gap-1 hover:text-primary transition-colors focus-visible:outline-none">
                            <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
                            <span>Ajouter un chapitre</span>
                        </button>
                    </div>
                    <div class="flex flex-col gap-2">
                        <template x-for="(chapitre, index) in chapitres" :key="index">
                            <div class="flex items-center gap-2.5 bg-surface-container-low border border-outline-variant rounded-xl p-2 group hover:border-secondary transition-colors">
                                <i data-lucide="grip-vertical" class="w-4 h-4 text-outline cursor-grab flex-shrink-0" aria-hidden="true"></i>
                                <input type="text" :name="'chapitres[' + index + ']'" x-model="chapitres[index]" placeholder="Titre du chapitre"
                                       class="flex-grow bg-transparent border-none text-xs focus:ring-0 p-0 text-on-surface">
                                <button type="button" @click="remove(index)" aria-label="Supprimer ce chapitre" class="text-outline hover:text-error transition-colors p-1 rounded-lg">
                                    <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i>
                                </button>
                            </div>
                        </template>
                        <p x-show="chapitres.length === 0" class="text-on-surface-variant text-xs">Aucun chapitre renseigné pour cet ouvrage.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-5 border-t border-outline-variant/60 flex flex-wrap justify-end items-center gap-3">
            <a href="{{ route('books.index') }}" class="text-xs font-semibold text-on-surface-variant px-4 py-2.5 rounded-xl hover:bg-surface-container transition-colors">
                Annuler
            </a>
            <button type="submit" name="action" value="save_draft"
                    class="text-xs font-semibold text-primary border border-outline-variant px-4 py-2.5 rounded-xl hover:bg-surface-container transition-colors shadow-2xs">
                Enregistrer sans valider
            </button>
            <button type="submit" name="action" value="validate"
                    class="bg-secondary text-white text-xs font-semibold px-4 py-2.5 rounded-xl hover:bg-primary transition-all shadow-xs flex items-center gap-1.5 hover:shadow hover:-translate-y-0.5 active:translate-y-0">
                <i data-lucide="check-circle-2" class="w-4 h-4" aria-hidden="true"></i>
                <span>Enregistrer et valider</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function chapterEditor(initial) {
        return {
            chapitres: initial && initial.length ? initial : [],
            add() {
                this.chapitres.push('');
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },
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

        // GSAP Micro-animation de confirmation visuelle (/gsap-performance)
        if (window.gsap) {
            gsap.from(img, { scale: 0.9, opacity: 0.5, duration: 0.35, ease: "back.out(1.5)" });
        }
    }

    function coverImageFailed(image) {
        image.classList.add('hidden');
        document.getElementById('cover-placeholder')?.classList.remove('hidden');
    }
</script>
@endpush

@endsection
