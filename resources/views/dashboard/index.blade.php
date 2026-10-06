@extends('layouts.app')

@section('title', 'BookManager - Tableau de bord')

@section('content')

<div>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Activité récente</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Voici un aperçu de vos statistiques et de vos derniers scans.</p>
</div>

{{-- Cartes de statistiques --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md md:gap-lg">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
        <div class="flex justify-between items-start mb-md">
            <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Livres enregistrés</span>
            <div class="p-sm bg-surface-container-high rounded-lg text-primary">
                <span class="material-symbols-outlined icon-fill">auto_stories</span>
            </div>
        </div>
        <div class="flex items-baseline gap-sm">
            <span class="font-display-lg text-display-lg text-on-surface">{{ number_format($totalBooks, 0, ',', ' ') }}</span>
            @if (! is_null($growthPercent))
                <span class="font-label-sm text-label-sm text-secondary">{{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}%</span>
            @endif
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
        <div class="flex justify-between items-start mb-md">
            <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Ajoutés aujourd'hui</span>
            <div class="p-sm bg-secondary-container rounded-lg text-on-secondary-container">
                <span class="material-symbols-outlined icon-fill">barcode_scanner</span>
            </div>
        </div>
        <span class="font-display-lg text-display-lg text-on-surface">{{ $addedToday }}</span>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
        <div class="flex justify-between items-start mb-md">
            <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Catégories</span>
            <div class="p-sm bg-surface-variant rounded-lg text-primary-container">
                <span class="material-symbols-outlined icon-fill">category</span>
            </div>
        </div>
        <div class="flex items-baseline gap-sm">
            <span class="font-display-lg text-display-lg text-on-surface">{{ $totalCategories }}</span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">Actives</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
        <div class="absolute top-0 right-0 w-16 h-16 bg-error-container rounded-bl-full -mr-8 -mt-8 opacity-20 group-hover:scale-110 transition-transform"></div>
        <div class="flex justify-between items-start mb-md relative z-10">
            <span class="font-label-md text-label-md text-error uppercase tracking-wider font-bold">À vérifier</span>
            <div class="p-sm bg-error-container rounded-lg text-on-error-container">
                <span class="material-symbols-outlined icon-fill">warning</span>
            </div>
        </div>
        <div class="flex items-baseline gap-sm relative z-10">
            <span class="font-display-lg text-display-lg text-on-surface">{{ $toVerifyCount }}</span>
            <span class="font-label-sm text-label-sm text-on-surface-variant">En attente d'approbation</span>
        </div>
    </div>
</section>

{{-- Graphiques --}}
<section class="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg">
    {{-- Histogramme --}}
    <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col hover:shadow-md transition-shadow duration-300">
        <div class="flex justify-between items-center mb-xl">
            <h3 class="font-headline-md text-headline-md text-on-surface">Évolution des ajouts <span class="text-on-surface-variant font-body-md text-body-md font-normal ml-xs">(7 derniers jours)</span></h3>
        </div>
        <div class="flex-1 w-full min-h-[240px] relative mt-auto flex items-end justify-between gap-2 sm:gap-4 border-b border-outline-variant pb-xs pt-8">
            @foreach ($weekly as $day)
                @php $heightPct = $day['count'] > 0 ? max(6, round(($day['count'] / $weeklyMax) * 100)) : 4; @endphp
                <div class="flex-1 flex flex-col justify-end items-center gap-2 group h-full">
                    <span class="text-on-surface-variant font-label-sm text-label-sm">{{ $day['count'] }}</span>
                    <div class="w-full max-w-[40px] bg-secondary-fixed-dim rounded-t-md group-hover:bg-secondary transition-colors" style="height: {{ $heightPct }}%"></div>
                    <span class="text-on-surface-variant font-label-sm text-label-sm">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Donut --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col items-center justify-between hover:shadow-md transition-shadow duration-300">
        <div class="w-full flex justify-between items-center mb-lg">
            <h3 class="font-headline-md text-headline-md text-on-surface">Répartition</h3>
        </div>

        @if ($repartition->isEmpty())
            <p class="text-on-surface-variant font-body-md text-body-md text-center py-lg">Aucune rubrique pour le moment.</p>
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
            <div class="relative w-48 h-48 rounded-full shadow-inner mb-lg" style="background: conic-gradient({{ $gradient }});">
                <div class="absolute inset-4 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-sm">
                    <div class="text-center">
                        <span class="block font-display-lg text-[32px] font-bold text-on-surface leading-none">{{ $totalCategories }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Rubriques</span>
                    </div>
                </div>
            </div>
            <div class="w-full space-y-sm">
                @foreach ($repartition as $r)
                    <div class="flex items-center justify-between font-label-md text-label-md">
                        <div class="flex items-center gap-xs">
                            <span class="w-3 h-3 rounded-sm" style="background-color: {{ $r['couleur'] }}"></span>
                            <span class="text-on-surface">{{ $r['nom'] }}</span>
                        </div>
                        <span class="text-on-surface-variant font-bold">{{ round(($r['count'] / $repartitionTotal) * 100) }}%</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Derniers ajouts --}}
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
    <div class="px-lg py-md border-b border-outline-variant flex justify-between items-center bg-surface-bright">
        <h3 class="font-headline-md text-headline-md text-on-surface">Derniers ajouts</h3>
        <a href="{{ route('books.index') }}" class="font-label-md text-label-md text-secondary hover:text-primary transition-colors flex items-center gap-xs">
            Voir tout <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/50">
                    <th class="px-lg py-sm font-label-md text-label-md text-on-surface-variant font-semibold w-24">Couverture</th>
                    <th class="px-lg py-sm font-label-md text-label-md text-on-surface-variant font-semibold">Titre &amp; ISBN</th>
                    <th class="px-lg py-sm font-label-md text-label-md text-on-surface-variant font-semibold">Auteur</th>
                    <th class="px-lg py-sm font-label-md text-label-md text-on-surface-variant font-semibold w-32">Date</th>
                    <th class="px-lg py-sm font-label-md text-label-md text-on-surface-variant font-semibold w-16"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30 bg-surface-container-lowest">
                @forelse ($recentBooks as $book)
                    <tr class="hover:bg-surface-dim/30 transition-colors group">
                        <td class="px-lg py-md">
                            <div class="w-12 h-16 bg-surface-container rounded border border-outline-variant overflow-hidden shadow-sm flex items-center justify-center text-outline">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="Couverture" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-[24px]">broken_image</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-lg py-md">
                            <div class="font-body-md text-body-md font-medium text-on-surface">{{ $book->titre }}</div>
                            <div class="font-label-sm text-label-sm text-on-surface-variant mt-xs font-mono">ISBN: {{ $book->isbn ?: '[Illisible]' }}</div>
                        </td>
                        <td class="px-lg py-md font-body-md text-body-md text-on-surface-variant {{ !$book->auteur ? 'italic text-outline' : '' }}">
                            {{ $book->auteur ?: 'Non détecté' }}
                        </td>
                        <td class="px-lg py-md font-body-md text-body-md text-on-surface-variant">{{ $book->created_at->diffForHumans() }}</td>
                        {{-- <td class="px-lg py-md">
                            @if ($book->statut === 'valide')
                                <span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-secondary-container/20 text-on-secondary-fixed font-label-sm text-label-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Validé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-error-container/40 text-on-error-container font-label-sm text-label-sm border border-error-container">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error"></span> À vérifier
                                </span>
                            @endif
                        </td> --}}
                        <td class="px-lg py-md text-right">
                            <a href="{{ route('books.edit', $book) }}" aria-label="Modifier" class="text-outline hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-lg py-xl text-center text-on-surface-variant font-body-md text-body-md">
                            Aucun livre pour le moment. <a href="{{ route('scan.index') }}" class="text-secondary hover:underline">Scannez votre premier livre</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
