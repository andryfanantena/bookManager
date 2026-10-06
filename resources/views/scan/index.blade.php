@extends('layouts.app')

@section('title', 'BookManager - Scanner')

@section('content')
<div x-data="bookScanner()">

    <header class="mb-xl">
            <h2 class="font-headline-lg text-headline-lg md:text-display-lg text-on-surface">Rechercher un livre par ISBN</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Les informations sont recherchées dans Open Library, puis Google Books si nécessaire.</p>
    </header>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <div class="flex flex-col items-center justify-center border-b border-outline-variant/30 bg-surface-container-low/30 py-xl px-lg">
            <form @submit.prevent="analyzeIsbn" class="w-full max-w-md mx-auto space-y-md">
                <label for="isbn" class="block font-label-md text-on-surface">ISBN du livre</label>
                <input id="isbn" type="text" x-model="isbn" required maxlength="20"
                       placeholder="Ex. 978-2-07-036822-8" autocomplete="off"
                       class="w-full rounded-xl border border-outline-variant bg-surface px-md py-md text-on-surface focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                       :disabled="loading">
                <p class="text-on-surface-variant text-label-sm">ISBN-10 ou ISBN-13, avec ou sans tirets.</p>
                <button type="submit" :disabled="loading"
                        class="w-full bg-primary text-on-primary rounded-xl font-headline-md py-md flex items-center justify-center gap-sm shadow-lg hover:bg-primary-container disabled:opacity-50">
                    <span class="material-symbols-outlined">search</span>
                    Rechercher le livre
                </button>
            </form>

            {{-- État d'analyse --}}
            <div class="w-full max-w-md mx-auto text-center py-lg" x-show="loading" x-cloak>
                <div class="inline-flex items-center gap-sm text-primary font-label-md text-label-md">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Analyse en cours ...
                </div>
            </div>

            <p x-show="errorMessage" x-text="errorMessage" x-cloak class="text-error font-label-md text-label-md mt-md"></p>
        </div>

        {{-- Liste des recherches récentes --}}
        <div class="p-lg">
            <div class="flex items-center justify-between mb-md">
                <h3 class="font-headline-md text-on-surface">Derniers livres recherchés</h3>
                <a href="{{ route('books.index') }}" class="text-label-md text-primary font-semibold hover:underline">Voir tout</a>
            </div>
            <div class="space-y-sm">
                @forelse ($recentScans as $book)
                    <a href="{{ route('books.edit', $book) }}" class="flex items-center gap-md p-md bg-surface rounded-lg border border-outline-variant/30 hover:border-secondary/50 transition-colors">
                        <div class="w-12 h-16 bg-surface-dim rounded flex items-center justify-center text-outline overflow-hidden">
                            @if ($book->cover_url)
                                <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="">
                            @else
                                <span class="material-symbols-outlined">book</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-label-md text-on-surface font-bold">{{ $book->titre }}</h4>
                            <p class="text-label-sm text-on-surface-variant">{{ $book->auteur ?: 'Auteur inconnu' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-xs">
                            <span class="bg-surface-container-high text-primary px-sm py-xs rounded text-[10px] font-bold uppercase">ISBN</span>
                            <span class="text-[10px] text-outline">{{ $book->isbn ?: '—' }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-on-surface-variant font-body-md text-body-md text-center py-lg">Aucune recherche pour le moment.</p>
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
