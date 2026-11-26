@php
    $navLinks = [
        ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Tableau de bord'],
        ['route' => 'books.index', 'icon' => 'library', 'label' => 'Catalogue'],
        ['route' => 'categories.index', 'icon' => 'folder-tree', 'label' => 'Catégories'],
        ['route' => 'scan.index', 'icon' => 'scan-barcode', 'label' => 'Scanner'],
        ['route' => 'books.export.index', 'icon' => 'file-spreadsheet', 'label' => 'Exporter'],
    ];
@endphp

<aside id="primary-sidebar"
       class="bg-surface-container h-full w-64 left-0 top-0 fixed flex flex-col p-md z-40 hidden md:flex border-r border-outline-variant shadow-xs justify-between">
    <div>
        <div class="mb-xl flex items-center justify-between gap-2 px-2 pt-2">
            <!-- Logo & Titre de marque -->
            <a href="{{ route('dashboard') }}"
               class="flex min-w-0 flex-1 items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary rounded-xl"
               title="BookManager">
                <div class="w-10 h-10 rounded-[40px] bg-primary text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="book-open" class="w-5 h-5" aria-hidden="true"></i>
                </div>
                <div>
                    <span class="font-headline-md text-lg font-black text-primary tracking-tight block">BookManager</span>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-secondary">Bibliothèque</span>
                </div>
            </a>
        </div>

        <!-- Navigation Principale -->
        <nav class="space-y-1" aria-label="Navigation principale">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   @if (request()->routeIs($link['route'].'*')) aria-current="page" @endif
                   title="{{ $link['label'] }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-150 group text-sm font-medium
                       {{ request()->routeIs($link['route'].'*')
                           ? 'bg-white text-secondary font-bold shadow-xs border-r-4 border-secondary'
                           : 'text-on-surface-variant hover:bg-white/70 hover:text-on-surface hover:translate-x-1' }}">
                    <i data-lucide="{{ $link['icon'] }}"
                       class="w-5 h-5 flex-shrink-0 transition-colors {{ request()->routeIs($link['route'].'*') ? 'text-secondary' : 'text-outline group-hover:text-primary' }}"
                       aria-hidden="true"></i>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Carte utilisateur en bas de la sidebar -->
    <div class="pt-md border-t border-outline-variant/60">
        @auth
            <div class="p-2.5 bg-surface-container-lowest rounded-2xl border border-outline-variant/80 shadow-xs flex items-center justify-between gap-2">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary rounded-xl" title="Accéder à mon profil">
                    <div class="relative w-9 h-9 rounded-full overflow-hidden border border-primary/20 bg-surface-container flex-shrink-0 group-hover:ring-2 group-hover:ring-secondary/50 transition-all">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-on-surface truncate group-hover:text-secondary transition-colors">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-on-surface-variant flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>En ligne</span>
                        </p>
                    </div>
                </a>

                <!-- Bouton Déconnexion Sidebar -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        title="Se déconnecter"
                        aria-label="Se déconnecter"
                        class="w-8 h-8 rounded-xl flex items-center justify-center text-outline hover:text-error hover:bg-error-container/40 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error"
                    >
                        <i data-lucide="log-out" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        @else
            <div class="space-y-2">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-container transition-colors shadow-xs">
                    <i data-lucide="log-in" class="w-4 h-4" aria-hidden="true"></i>
                    <span>Connexion</span>
                </a>
            </div>
        @endauth
    </div>
</aside>
