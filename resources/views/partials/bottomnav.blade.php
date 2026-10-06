<nav class="md:hidden fixed bottom-0 w-full z-50 rounded-t-xl shadow-md border-t border-outline-variant bg-surface">
    <ul class="flex justify-around items-center px-margin-mobile pb-sm pt-xs">
        <li>
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[24px]">home</span>
                <span class="font-label-sm text-label-sm mt-1">Accueil</span>
            </a>
        </li>
        <li>
            <a href="{{ route('scan.index') }}"
               class="flex flex-col items-center justify-center rounded-full px-4 py-1 {{ request()->routeIs('scan.*') ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[24px]">document_scanner</span>
                <span class="font-label-sm text-label-sm mt-1">Scan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('books.index') }}" class="flex flex-col items-center justify-center p-2 rounded-lg {{ request()->routeIs('books.*') ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[24px]">auto_stories</span>
                <span class="font-label-sm text-label-sm mt-1">Catalogue</span>
            </a>
        </li>
        <li>
            <a href="{{ route('categories.index') }}" class="flex flex-col items-center justify-center p-2 rounded-lg {{ request()->routeIs('categories.*') ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[24px]">category</span>
                <span class="font-label-sm text-label-sm mt-1">Rubriques</span>
            </a>
        </li>
    </ul>
</nav>
