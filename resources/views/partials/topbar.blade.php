<header class="bg-surface sticky top-0 z-30 border-b border-outline-variant shadow-sm h-[72px] flex-shrink-0">
    <div class="flex justify-between items-center px-lg py-sm w-full h-full max-w-container-max mx-auto">

        <div class="flex items-center gap-md md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Ouvrir le menu" class="p-sm rounded-full hover:bg-surface-container-low transition-colors text-primary">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="font-headline-md text-headline-md font-black text-primary">BookManager</span>
        </div>

        <form action="{{ route('books.index') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-lg">
            <div class="relative w-full group">
                <div class="absolute inset-y-0 left-0 pl-sm flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-on-surface-variant group-focus-within:text-primary transition-colors">search</span>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un livre, un ISBN, une cote..."
                       class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary transition-shadow">
            </div>
        </form>

        <div class="flex items-center gap-sm md:gap-md ml-auto">
            <a href="{{ route('scan.index') }}" class="hidden sm:flex items-center gap-xs bg-primary text-on-primary px-md py-sm rounded-lg font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">barcode_scanner</span>
                Scanner
            </a>
            <div class="w-10 h-10 rounded-full border border-outline-variant overflow-hidden bg-surface-container-high flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </div>

    <!-- Menu mobile déroulant -->
    <div x-show="mobileMenuOpen" x-cloak @click.outside="mobileMenuOpen = false" class="md:hidden absolute top-[72px] left-0 w-full bg-surface-container border-b border-outline-variant shadow-md z-40">
        <nav class="flex flex-col p-md space-y-xs">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-dim">
                <span class="material-symbols-outlined">dashboard</span> Tableau de bord
            </a>
            <a href="{{ route('books.index') }}" class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-dim">
                <span class="material-symbols-outlined">library_books</span> Catalogue
            </a>
            <a href="{{ route('categories.index') }}" class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-dim">
                <span class="material-symbols-outlined">category</span> Catégories
            </a>
            <a href="{{ route('books.export.index') }}" class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-dim">
                <span class="material-symbols-outlined">table_view</span> Exporter
            </a>
        </nav>
    </div>
</header>
