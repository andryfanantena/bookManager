<header class="bg-surface sticky top-0 z-30 border-b border-outline-variant shadow-xs h-[72px] flex-shrink-0" x-data="{ userMenuOpen: false }">
    <div class="flex justify-between items-center px-4 sm:px-6 lg:px-8 py-2 w-full h-full max-w-container-max mx-auto">

        <!-- Bouton hamburger mobile & Logo -->
        <div class="flex items-center gap-3 md:hidden">
        <button @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen"
                aria-controls="mobile-navigation"
                :aria-label="mobileMenuOpen ? 'Fermer le menu de navigation' : 'Ouvrir le menu de navigation'"
                class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
            <i data-lucide="menu" class="w-5 h-5" aria-hidden="true"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center">
                    <i data-lucide="book-open" class="w-4 h-4" aria-hidden="true"></i>
                </div>
                <span class="font-headline-md text-base font-black text-primary">BookManager</span>
            </a>
        </div>

        <!-- Barre de recherche rapide -->
        <form action="{{ route('books.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-6">
            <div class="relative w-full group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                    <i data-lucide="search" class="w-4 h-4" aria-hidden="true"></i>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Rechercher par titre, ISBN, auteur..."
                    class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                >
            </div>
        </form>

        <!-- Actions droites & Profil Utilisateur -->
        <div class="flex items-center gap-2 sm:gap-3 ml-auto">
            {{-- <a href="{{ route('scan.index') }}" class="hidden sm:inline-flex items-center gap-2 bg-primary text-white px-3.5 py-2 rounded-xl text-xs font-semibold shadow-xs hover:bg-primary-container transition-all hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                <i data-lucide="scan-barcode" class="w-4 h-4" aria-hidden="true"></i>
                <span>Scanner</span>
            </a> --}}

            @auth
                <!-- Menu Déroulant Profil Utilisateur -->
                <div class="relative">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        @keydown.escape="userMenuOpen = false"
                        class="flex items-center gap-2 p-1.5 pl-2 pr-2.5 rounded-full hover:bg-surface-container transition-colors border border-outline-variant/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        aria-expanded="false"
                        :aria-expanded="userMenuOpen"
                        aria-label="Menu profil utilisateur"
                    >
                        <div class="w-8 h-8 rounded-full border border-primary/20 overflow-hidden bg-surface-container flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        </div>
                        <span class="hidden md:block text-xs font-semibold text-on-surface max-w-[120px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-outline transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" aria-hidden="true"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div
                        x-show="userMenuOpen"
                        x-cloak
                        @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant py-2 z-50 text-on-surface"
                    >
                        <!-- En-tête du menu -->
                        <div class="px-4 py-3 border-b border-outline-variant/50 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full border border-primary/20 overflow-hidden bg-surface-container flex-shrink-0">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-on-surface truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-on-surface-variant font-mono truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <!-- Liens du menu -->
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" @click="userMenuOpen = false" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-on-surface hover:bg-surface-container transition-colors">
                                <i data-lucide="user-cog" class="w-4 h-4 text-secondary" aria-hidden="true"></i>
                                <span>Mon Profil &amp; Photo</span>
                            </a>
                            <a href="{{ route('books.index') }}" @click="userMenuOpen = false" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-on-surface hover:bg-surface-container transition-colors">
                                <i data-lucide="library" class="w-4 h-4 text-primary" aria-hidden="true"></i>
                                <span>Catalogue de livres</span>
                            </a>
                            <a href="{{ route('scan.index') }}" @click="userMenuOpen = false" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-on-surface hover:bg-surface-container transition-colors">
                                <i data-lucide="scan-line" class="w-4 h-4 text-primary" aria-hidden="true"></i>
                                <span>Scanner un livre</span>
                            </a>
                        </div>

                        <div class="border-t border-outline-variant/50 pt-1 mt-1">
                            <!-- Bouton Déconnexion -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2 text-xs font-semibold text-error hover:bg-error-container/40 transition-colors text-left"
                                >
                                    <i data-lucide="log-out" class="w-4 h-4 text-error" aria-hidden="true"></i>
                                    <span>Se déconnecter</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-2 rounded-xl text-primary hover:bg-surface-container transition-colors">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="text-xs font-semibold px-3.5 py-2 bg-primary text-white rounded-xl hover:bg-primary-container transition-colors shadow-xs">
                        Inscription
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Menu mobile déroulant -->
    <div id="mobile-navigation" x-show="mobileMenuOpen" x-cloak @click.outside="mobileMenuOpen = false" class="md:hidden absolute top-[72px] left-0 w-full bg-surface-container border-b border-outline-variant shadow-lg z-40">
        @auth
            <!-- En-tête profil mobile -->
            <div class="px-4 py-3 bg-surface-dim/40 border-b border-outline-variant flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border border-primary/20 overflow-hidden bg-white flex-shrink-0">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-on-surface truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-on-surface-variant font-mono truncate">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" aria-label="Modifier le profil" class="p-2 text-secondary hover:text-primary rounded-xl bg-white/70">
                    <i data-lucide="pencil" class="w-4 h-4" aria-hidden="true"></i>
                </a>
            </div>
        @endauth

        <nav class="flex flex-col p-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('dashboard') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('books.*') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="library" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Catalogue</span>
            </a>
            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('categories.*') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="folder-tree" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Catégories</span>
            </a>
            <a href="{{ route('scan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('scan.*') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="scan-barcode" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Scanner</span>
            </a>
            <a href="{{ route('books.export.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('books.export.*') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Exporter</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('profile.*') ? 'bg-white font-bold text-primary shadow-xs' : 'text-on-surface-variant hover:bg-white/60' }}">
                <i data-lucide="user-cog" class="w-5 h-5 text-secondary" aria-hidden="true"></i>
                <span>Mon Profil &amp; Photo</span>
            </a>

            @auth
                <!-- Bouton de déconnexion mobile -->
                <div class="pt-2 border-t border-outline-variant/60">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm text-error hover:bg-error-container/40 font-semibold transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5" aria-hidden="true"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            @endauth
        </nav>
    </div>
</header>
