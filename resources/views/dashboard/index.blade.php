@extends('layouts.app')

@section('title', 'BookManager - Tableau de bord')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs font-semibold text-secondary uppercase tracking-wider mb-1">
            <i data-lucide="layout-dashboard" class="w-4 h-4" aria-hidden="true"></i>
            <span>Vue d'ensemble</span>
        </div>
        <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-on-surface">Tableau de bord</h2>
        <p class="font-body-md text-sm sm:text-base text-on-surface-variant mt-1">Aperçu en temps réel de votre catalogue et de vos derniers enregistrements.</p>
    </div>
    <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <a href="{{ route('scan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-xs sm:text-sm font-semibold shadow-xs hover:bg-primary-container transition-all hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
            <i data-lucide="scan-barcode" class="w-4 h-4" aria-hidden="true"></i>
            <span>Nouveau scan</span>
        </a>
        <a href="{{ route('books.export.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-surface-container-lowest border border-outline-variant text-on-surface rounded-xl text-xs sm:text-sm font-semibold shadow-2xs hover:bg-surface-container transition-all hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-secondary" aria-hidden="true"></i>
            <span class="hidden sm:inline">Exporter</span>
        </a>
    </div>
</div>

{{-- Cartes de statistiques animées avec GSAP ScrollTrigger.batch --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" aria-label="Statistiques clés">
    <!-- Stat 1 : Livres enregistrés -->
    <div class="gsap-card bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col justify-between shadow-xs">
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Livres enregistrés</span>
            <div class="w-10 h-10 bg-surface-container-high rounded-xl text-primary flex items-center justify-center flex-shrink-0">
                <i data-lucide="book-open" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl text-on-surface font-extrabold stat-counter" data-target="{{ $totalBooks }}">{{ $totalBooks }}</span>
            @if (! is_null($growthPercent))
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $growthPercent >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}%
                </span>
            @endif
        </div>
    </div>

    <!-- Stat 2 : Ajoutés aujourd'hui -->
    <div class="gsap-card bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col justify-between shadow-xs">
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Ajoutés aujourd'hui</span>
            <div class="w-10 h-10 bg-secondary-container rounded-xl text-white flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-check" class="w-5 h-5" aria-hidden="true"></i>
            </div>
        </div>
        <span class="text-3xl sm:text-4xl text-on-surface font-extrabold stat-counter" data-target="{{ $addedToday }}">{{ $addedToday }}</span>
    </div>

    <!-- Stat 3 : Catégories -->
    <div class="gsap-card bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col justify-between shadow-xs">
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Catégories</span>
            <div class="w-10 h-10 bg-surface-container rounded-xl text-primary flex items-center justify-center flex-shrink-0">
                <i data-lucide="tags" class="w-5 h-5 text-primary" aria-hidden="true"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl sm:text-4xl text-on-surface font-extrabold stat-counter" data-target="{{ $totalCategories }}">{{ $totalCategories }}</span>
            <span class="text-xs text-on-surface-variant font-medium">Actives</span>
        </div>
    </div>

    <!-- Stat 4 : À vérifier -->
    <div class="gsap-card bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col justify-between relative overflow-hidden group shadow-xs">
        <div class="absolute top-0 right-0 w-16 h-16 bg-rose-100 rounded-bl-full -mr-8 -mt-8 opacity-40 group-hover:scale-125 transition-transform duration-300 pointer-events-none"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <span class="text-xs text-rose-700 uppercase tracking-wider font-bold">À vérifier</span>
            <div class="w-10 h-10 bg-rose-100 rounded-xl text-rose-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5" aria-hidden="true"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2 relative z-10">
            <span class="text-3xl sm:text-4xl text-on-surface font-extrabold stat-counter" data-target="{{ $toVerifyCount }}">{{ $toVerifyCount }}</span>
            <span class="text-xs text-on-surface-variant">En attente</span>
        </div>
    </div>
</section>

{{-- Graphiques avec animation GSAP --}}
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Histogramme GPU-accelerated scaleY --}}
    <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col shadow-xs">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-headline-md text-lg sm:text-xl text-on-surface font-bold">Évolution des ajouts</h3>
                <span class="text-on-surface-variant text-xs">Fréquence des enregistrements sur les 7 derniers jours</span>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-high rounded-lg text-primary text-xs font-semibold">
                <i data-lucide="trending-up" class="w-4 h-4 text-secondary" aria-hidden="true"></i>
                <span>Activité récente</span>
            </div>
        </div>
        <div class="flex-1 w-full min-h-[220px] relative mt-auto flex items-end justify-between gap-2 sm:gap-4 border-b border-outline-variant pb-2 pt-6">
            @foreach ($weekly as $day)
                @php $heightPct = $day['count'] > 0 ? max(8, round(($day['count'] / $weeklyMax) * 100)) : 4; @endphp
                <div class="flex-1 flex flex-col justify-end items-center gap-2 group h-full">
                    <span class="text-on-surface-variant text-[11px] font-semibold opacity-0 group-hover:opacity-100 transition-opacity">{{ $day['count'] }}</span>
                    <div class="chart-bar w-full max-w-[42px] bg-secondary-fixed-dim rounded-t-xl group-hover:bg-secondary transition-colors cursor-pointer relative shadow-2xs" style="height: {{ $heightPct }}%">
                        <!-- Tooltip flottant -->
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-primary text-white text-[10px] font-bold rounded-md opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-20 shadow-xs">
                            {{ $day['count'] }} livre{{ $day['count'] > 1 ? 's' : '' }}
                        </div>
                    </div>
                    <span class="text-on-surface-variant text-[11px] font-medium">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Donut Répartition --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-between shadow-xs">
        <div class="w-full flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-lg sm:text-xl text-on-surface font-bold">Répartition</h3>
            <span class="text-xs text-on-surface-variant font-medium">Par rubrique</span>
        </div>

        @if ($repartition->isEmpty())
            <div class="py-12 text-center text-on-surface-variant space-y-2">
                <i data-lucide="pie-chart" class="w-10 h-10 text-outline mx-auto" aria-hidden="true"></i>
                <p class="text-sm">Aucune rubrique pour le moment.</p>
            </div>
        @else
            @php
                $gradientParts = [];
                $cursor = 0;
                foreach ($repartition as $r) {
                    $pct = round(($r['count'] / $repartitionTotal) * 100);
                    $gradientParts[] = "{$r['couleur']} {$cursor}% " . ($cursor + $pct) . '%';
                    $cursor += $pct;
                }
                $gradient = implode(', ', $gradientParts);
            @endphp
            <div id="repartition-donut" class="relative w-44 h-44 rounded-full shadow-inner mb-6 transition-transform duration-300 hover:rotate-6" style="background: conic-gradient({{ $gradient }});">
                <div class="absolute inset-4 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-xs">
                    <div class="text-center">
                        <span class="block text-2xl sm:text-3xl font-extrabold text-on-surface leading-none">{{ $totalCategories }}</span>
                        <span class="text-[11px] text-on-surface-variant font-medium">Rubriques</span>
                    </div>
                </div>
            </div>
            <div class="w-full space-y-2 max-h-48 overflow-y-auto pr-1">
                @foreach ($repartition as $r)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-outline-variant/40 last:border-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $r['couleur'] }}"></span>
                            <span class="text-on-surface font-medium truncate">{{ $r['nom'] }}</span>
                        </div>
                        <span class="text-on-surface-variant font-bold ml-2">{{ round(($r['count'] / $repartitionTotal) * 100) }}%</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Derniers ajouts --}}
<section class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-xs">
    <div class="px-5 sm:px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-bright">
        <div>
            <h3 class="font-headline-md text-lg sm:text-xl text-on-surface font-bold">Derniers ajouts</h3>
            <span class="text-xs text-on-surface-variant">Livres récemment numérisés ou créés</span>
        </div>
        <a href="{{ route('books.index') }}" class="text-xs sm:text-sm text-secondary hover:text-primary transition-colors flex items-center gap-1.5 font-semibold group">
            <span>Voir tout le catalogue</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" aria-hidden="true"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/60 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">
                    <th class="px-6 py-3 w-20">Couverture</th>
                    <th class="px-6 py-3">Titre &amp; ISBN</th>
                    <th class="px-6 py-3">Auteur</th>
                    <th class="px-6 py-3 w-36">Enregistré</th>
                    <th class="px-6 py-3 w-20 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40 bg-surface-container-lowest">
                @forelse ($recentBooks as $book)
                    <tr class="hover:bg-surface-dim/40 transition-colors group">
                        <td class="px-6 py-3.5">
                            <div class="w-11 h-14 bg-surface-container rounded-lg border border-outline-variant overflow-hidden shadow-2xs flex items-center justify-center text-outline group-hover:scale-105 transition-transform duration-200">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->titre }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="book" class="w-5 h-5 text-outline" aria-hidden="true"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('books.edit', $book) }}" class="text-sm font-semibold text-on-surface group-hover:text-secondary transition-colors block">
                                {{ $book->titre }}
                            </a>
                            <div class="text-[11px] text-on-surface-variant mt-0.5 font-mono">
                                ISBN: {{ $book->isbn ?: '[Non renseigné]' }}
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-xs text-on-surface-variant {{ !$book->auteur ? 'italic text-outline' : '' }}">
                            {{ $book->auteur ?: 'Non détecté' }}
                        </td>
                        <td class="px-6 py-3.5 text-xs text-on-surface-variant">
                            {{ $book->created_at ? $book->created_at->diffForHumans() : 'Récemment' }}
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <a href="{{ route('books.edit', $book) }}" aria-label="Modifier l'ouvrage {{ $book->titre }}" class="inline-flex p-2 rounded-xl text-outline hover:text-primary hover:bg-surface-container transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant text-sm">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-surface-container text-outline flex items-center justify-center mx-auto">
                                    <i data-lucide="book-open" class="w-6 h-6" aria-hidden="true"></i>
                                </div>
                                <p class="font-medium text-on-surface">Aucun livre pour le moment.</p>
                                <a href="{{ route('scan.index') }}" class="inline-flex items-center gap-1.5 text-secondary font-semibold hover:underline text-xs">
                                    <i data-lucide="scan-barcode" class="w-4 h-4" aria-hidden="true"></i>
                                    <span>Scannez votre premier livre</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
