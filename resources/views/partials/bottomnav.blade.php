<nav class="md:hidden fixed bottom-0 w-full z-50 rounded-t-2xl shadow-lg border-t border-outline-variant bg-surface/95 backdrop-blur-md" aria-label="Navigation mobile">
    <ul class="flex justify-around items-center px-2 py-1.5 min-h-[58px]">
        <li>
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center min-w-[52px] min-h-[44px] px-1 py-1 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-secondary' : '' }}" aria-hidden="true"></i>
                <span class="text-[10px] mt-0.5 tracking-tight">Accueil</span>
            </a>
        </li>
        <li>
            <a href="{{ route('books.index') }}" class="flex flex-col items-center justify-center min-w-[52px] min-h-[44px] px-1 py-1 rounded-xl transition-colors {{ request()->routeIs('books.*') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                <i data-lucide="library" class="w-5 h-5 {{ request()->routeIs('books.*') ? 'text-secondary' : '' }}" aria-hidden="true"></i>
                <span class="text-[10px] mt-0.5 tracking-tight">Catalogue</span>
            </a>
        </li>
        <li>
            <a href="{{ route('scan.index') }}"
               class="flex flex-col items-center justify-center rounded-2xl px-3.5 py-1.5 transition-all min-h-[44px] {{ request()->routeIs('scan.*') ? 'bg-secondary text-white font-bold shadow-md' : 'bg-surface-container-high text-primary hover:bg-surface-dim' }}">
                <i data-lucide="scan-barcode" class="w-5 h-5" aria-hidden="true"></i>
                <span class="text-[10px] mt-0.5 font-semibold tracking-tight">Scanner</span>
            </a>
        </li>
        <li>
            <a href="{{ route('categories.index') }}" class="flex flex-col items-center justify-center min-w-[52px] min-h-[44px] px-1 py-1 rounded-xl transition-colors {{ request()->routeIs('categories.*') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                <i data-lucide="folder-tree" class="w-5 h-5 {{ request()->routeIs('categories.*') ? 'text-secondary' : '' }}" aria-hidden="true"></i>
                <span class="text-[10px] mt-0.5 tracking-tight">Rubriques</span>
            </a>
        </li>
        <li>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center min-w-[52px] min-h-[44px] px-1 py-1 rounded-xl transition-colors {{ request()->routeIs('profile.*') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                <i data-lucide="user" class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-secondary' : '' }}" aria-hidden="true"></i>
                <span class="text-[10px] mt-0.5 tracking-tight">Profil</span>
            </a>
        </li>
    </ul>
</nav>
