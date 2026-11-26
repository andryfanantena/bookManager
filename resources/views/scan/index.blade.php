@extends('layouts.app')

@section('title', 'BookManager - Scanner')

@section('content')
<div x-data="bookScanner()" class="space-y-6 sm:space-y-8">

    <header class="mb-6">
        <div class="flex items-center gap-2 text-xs font-semibold text-secondary uppercase tracking-wider mb-1">
            <i data-lucide="scan-line" class="w-4 h-4" aria-hidden="true"></i>
            <span>Recherche &amp; Enrichissement</span>
        </div>
        <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-on-surface">Rechercher un livre par ISBN</h2>
        <p class="font-body-md text-sm sm:text-base text-on-surface-variant mt-1">Interrogation multi-sources : Google Books, Open Library et Gallica (BnF) pour un catalogage automatisé.</p>
    </header>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-xs">
        <div class="flex flex-col items-center justify-center border-b border-outline-variant/60 bg-gradient-to-b from-surface to-surface-container/30 py-10 px-4 sm:px-6">

            <!-- Graphic scanner laser avec animation GSAP transform y (/gsap-performance) -->
            <div class="relative w-40 h-28 mb-6 border-2 border-dashed border-secondary/40 rounded-2xl flex items-center justify-center bg-white shadow-inner overflow-hidden">
                <i data-lucide="scan-barcode" class="w-14 h-14 text-outline/40" aria-hidden="true"></i>
                <!-- Faisceau laser animé GSAP via transforms GPU -->
                <div id="scanner-laser" class="absolute left-0 right-0 top-0 h-0.5 bg-gradient-to-r from-transparent via-red-500 to-transparent shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
            </div>

            <form @submit.prevent="analyzeIsbn" class="w-full max-w-md mx-auto space-y-4">
                <div>
                    <label for="isbn" class="block text-xs sm:text-sm text-on-surface font-semibold mb-1.5">Numéro ISBN de l'ouvrage</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline">
                            <i data-lucide="barcode" class="w-5 h-5" aria-hidden="true"></i>
                        </div>
                        <input
                            id="isbn"
                            type="text"
                            x-model="isbn"
                            required
                            maxlength="20"
                            placeholder="Ex. 978-2-07-036822-8"
                            autocomplete="off"
                            class="w-full pl-11 pr-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/30 text-sm font-mono shadow-2xs transition-all"
                            :disabled="loading"
                        >
                    </div>
                    <div class="flex justify-between items-center mt-1.5 text-xs text-on-surface-variant">
                        <span>Format ISBN-10 ou ISBN-13</span>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-primary text-white rounded-xl py-3 px-4 flex items-center justify-center gap-2 font-semibold text-sm shadow-xs hover:bg-primary-container disabled:opacity-50 transition-all hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <i data-lucide="search" class="w-4 h-4" x-show="!loading" aria-hidden="true"></i>
                    <span x-text="loading ? 'Recherche des métadonnées...' : 'Rechercher le livre'"></span>
                </button>
            </form>

            {{-- État d'analyse --}}
            <div class="w-full max-w-md mx-auto text-center py-4" x-show="loading" x-cloak>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-container-high text-primary font-medium text-xs sm:text-sm">
                    <svg class="animate-spin h-4 w-4 text-secondary" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Interrogation des sources BnF, Google &amp; OpenLibrary...</span>
                </div>
            </div>

            <p x-show="errorMessage" x-text="errorMessage" x-cloak class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-medium mt-4 max-w-md w-full" role="alert"></p>
        </div>

        {{-- Liste des recherches récentes --}}
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-headline-md text-base sm:text-lg text-on-surface font-bold">Derniers livres recherchés</h3>
                <a href="{{ route('books.index') }}" class="text-xs text-secondary font-bold hover:underline flex items-center gap-1 group">
                    <span>Voir tout</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" aria-hidden="true"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse ($recentScans as $book)
                    <a href="{{ route('books.edit', $book) }}" class="gsap-card flex items-start gap-3 p-3 bg-surface-container-lowest rounded-xl border border-outline-variant/70 hover:border-secondary transition-all group">
                        <div class="w-12 h-16 bg-surface-container rounded-lg flex items-center justify-center text-outline overflow-hidden flex-shrink-0 shadow-2xs group-hover:scale-105 transition-transform duration-200">
                            @if ($book->cover_url)
                                <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="{{ $book->titre }}" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                                <i data-lucide="book" class="hidden w-5 h-5 text-outline" aria-hidden="true"></i>
                            @else
                                <i data-lucide="book" class="w-5 h-5 text-outline" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-xs sm:text-sm text-on-surface truncate group-hover:text-secondary transition-colors">{{ $book->titre }}</h4>
                            <p class="text-xs text-on-surface-variant whitespace-normal break-words mt-0.5">{{ $book->auteur ?: 'Auteur non renseigné' }}</p>
                            <span class="inline-block mt-1 font-mono text-[10px] text-outline bg-surface-container px-1.5 py-0.5 rounded">
                                {{ $book->isbn ?: 'Sans ISBN' }}
                            </span>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-outline group-hover:text-secondary group-hover:translate-x-0.5 transition-all" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="col-span-2 py-8 text-center text-on-surface-variant text-sm">
                        Aucune recherche enregistrée pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookScanner() {
    return {
        isbn: '',
        loading: false,
        errorMessage: null,

        async analyzeIsbn() {
            this.loading = true;
            this.errorMessage = null;

            const body = new FormData();
            body.append('isbn', this.isbn);

            try {
                const response = await fetch('{{ route('scan.analyze') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body,
                });

                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json')
                    ? await response.json()
                    : {};

                if (!response.ok) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    throw new Error(data.errors?.isbn?.[0] || data.message || 'Le serveur a renvoyé une réponse inattendue.');
                }

                window.location.href = data.redirect;
            } catch (e) {
                this.loading = false;
                this.errorMessage = e.message;
            }
        },
    };
}
</script>
@endpush

@endsection
