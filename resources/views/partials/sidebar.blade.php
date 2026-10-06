@php
    $navLinks = [
        ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Tableau de bord'],
        ['route' => 'books.index', 'icon' => 'library_books', 'label' => 'Catalogue'],
        ['route' => 'categories.index', 'icon' => 'category', 'label' => 'Catégories'],
        ['route' => 'scan.index', 'icon' => 'barcode_scanner', 'label' => 'Scanner'],
    ];
@endphp

<aside class="bg-surface-container h-full w-64 left-0 top-0 fixed flex-col p-md z-40 hidden md:flex border-r border-outline-variant shadow-sm">
    <div class="flex items-center gap-sm mb-xl px-sm pt-sm">
        <span class="material-symbols-outlined icon-fill text-primary text-[32px]">auto_stories</span>
        <span class="font-headline-md text-headline-md font-black text-primary">BookManager</span>
    </div>

    <nav class="flex-1 space-y-xs">
        @foreach ($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               @if (request()->routeIs($link['route'].'*')) aria-current="page" @endif
               class="flex items-center gap-md px-md py-sm rounded-lg transition-colors group
                   {{ request()->routeIs($link['route'].'*')
                       ? 'bg-surface-dim text-primary font-bold border-r-4 border-secondary'
                       : 'text-on-surface-variant hover:bg-surface-dim' }}">
                <span class="material-symbols-outlined {{ request()->routeIs($link['route'].'*') ? 'icon-fill' : 'group-hover:text-primary transition-colors' }}">{{ $link['icon'] }}</span>
                <span class="font-label-md text-label-md">{{ $link['label'] }}</span>
            </a>
        @endforeach

        <a href="{{ route('books.export.pdf') }}" class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-dim transition-colors group">
            <span class="material-symbols-outlined group-hover:text-primary transition-colors">file_download</span>
            <span class="font-label-md text-label-md">Exporter</span>
        </a>
    </nav>
</aside>
