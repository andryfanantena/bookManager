<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'BookManager')</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Vector Icons - Lucide (conforme /icon-design, aucun émoji) -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- GSAP Core & Plugins (conforme /gsap-timeline /gsap-scrolltrigger /gsap-plugins /gsap-performance) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#00236f",
                    "primary-container": "#1e3a8a",
                    "primary-hover": "#001b57",
                    "on-primary": "#ffffff",
                    "secondary": "#0058be",
                    "secondary-container": "#2170e4",
                    "secondary-fixed-dim": "#adc6ff",
                    "on-secondary": "#ffffff",
                    "surface": "#f9f9ff",
                    "surface-container": "#eef2ff",
                    "surface-container-low": "#f4f6ff",
                    "surface-container-high": "#dee8ff",
                    "surface-container-highest": "#d8e3fb",
                    "surface-container-lowest": "#ffffff",
                    "surface-dim": "#e0e7fa",
                    "surface-bright": "#ffffff",
                    "on-surface": "#0f172a",
                    "on-surface-variant": "#475569",
                    "outline": "#94a3b8",
                    "outline-variant": "#e2e8f0",
                    "error": "#dc2626",
                    "error-container": "#fee2e2",
                    "on-error-container": "#991b1b",
                    "background": "#f8fafc",
                    "on-background": "#0f172a",
                },
                borderRadius: { 
                    DEFAULT: "0.375rem", 
                    lg: "0.5rem", 
                    xl: "0.75rem", 
                    "2xl": "1rem", 
                    full: "9999px" 
                },
                spacing: {
                    xl: "32px", "2xl": "48px", "margin-mobile": "16px", sm: "8px",
                    md: "16px", xs: "4px", "3xl": "64px", gutter: "24px",
                    lg: "24px", base: "4px", "container-max": "1280px",
                },
                fontFamily: {
                    sans: ["Inter", "sans-serif"],
                    "headline-lg": ["Inter", "sans-serif"],
                    "headline-md": ["Inter", "sans-serif"],
                    "body-lg": ["Inter", "sans-serif"],
                    "body-md": ["Inter", "sans-serif"],
                    "label-md": ["Inter", "sans-serif"],
                    "label-sm": ["Inter", "sans-serif"],
                    "display-lg": ["Inter", "sans-serif"],
                },
                fontSize: {
                    "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700" }],
                    "headline-md": ["22px", { lineHeight: "30px", letterSpacing: "-0.01em", fontWeight: "600" }],
                    "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                    "body-md": ["15px", { lineHeight: "24px", fontWeight: "400" }],
                    "label-md": ["13px", { lineHeight: "18px", letterSpacing: "0.01em", fontWeight: "500" }],
                    "label-sm": ["11px", { lineHeight: "16px", letterSpacing: "0.04em", fontWeight: "600" }],
                    "display-lg": ["40px", { lineHeight: "48px", letterSpacing: "-0.03em", fontWeight: "800" }],
                },
            },
        },
    };
</script>

<style>
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

    /* Performance GPU et micro-interactions (/gsap-performance & /ui-ux-pro-max) */
    .gsap-card {
        will-change: transform, box-shadow;
        transition: transform 0.22s cubic-bezier(0.2, 0, 0.2, 1), box-shadow 0.22s cubic-bezier(0.2, 0, 0.2, 1);
    }
    .gsap-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(0, 35, 111, 0.08), 0 4px 8px -4px rgba(0, 35, 111, 0.04);
    }
    .chart-bar {
        will-change: transform;
    }
</style>
@stack('styles')
</head>
<body class="bg-background text-on-background font-body-md min-h-screen antialiased flex flex-col" x-data="{
    mobileMenuOpen: false
}">

@include('partials.sidebar')

<div class="flex-1 flex flex-col min-w-0 md:ml-64 relative bg-background">
    @include('partials.topbar')

    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-12">
        <div class="max-w-container-max mx-auto space-y-6 sm:space-y-8">

            {{-- Flash Messages Stylisés avec Icônes Vectorielles Lucide --}}
            @if (session('success'))
                <div id="flash-success" class="bg-emerald-50 border border-emerald-200 text-emerald-950 px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-xs" role="alert">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check-circle-2" class="w-5 h-5" aria-hidden="true"></i>
                        </span>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-success').remove()" aria-label="Fermer la notification" class="p-1 rounded-lg text-emerald-700 hover:bg-emerald-100 transition-colors">
                        <i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>
            @endif

            @if (session('warning'))
                <div id="flash-warning" class="bg-amber-50 border border-amber-200 text-amber-950 px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-xs" role="alert">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5" aria-hidden="true"></i>
                        </span>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-warning').remove()" aria-label="Fermer l'avertissement" class="p-1 rounded-lg text-amber-700 hover:bg-amber-100 transition-colors">
                        <i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div id="flash-error" class="bg-rose-50 border border-rose-200 text-rose-950 px-5 py-4 rounded-2xl shadow-xs" role="alert">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2.5 font-bold text-sm text-rose-900">
                            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-4 h-4" aria-hidden="true"></i>
                            </span>
                            <span>Merci de vérifier les points suivants :</span>
                        </div>
                        <button onclick="document.getElementById('flash-error').remove()" aria-label="Fermer l'alerte d'erreur" class="p-1 rounded-lg text-rose-700 hover:bg-rose-100 transition-colors">
                            <i data-lucide="x" class="w-4 h-4" aria-hidden="true"></i>
                        </button>
                    </div>
                    <ul class="list-disc list-inside text-xs sm:text-sm text-rose-800 space-y-1 ml-9">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<!-- Bouton flottant Remonter en haut de page (GSAP ScrollToPlugin) -->
<button
    id="scroll-to-top"
    aria-label="Remonter en haut de la page"
    class="fixed bottom-20 md:bottom-6 right-6 z-40 w-11 h-11 rounded-full bg-primary text-white shadow-lg flex items-center justify-center hover:bg-primary-container transition-all opacity-0 pointer-events-none hover:scale-105 active:scale-95"
>
    <i data-lucide="arrow-up" class="w-5 h-5" aria-hidden="true"></i>
</button>

@include('partials.bottomnav')

<!-- Système d'animation GSAP & icônes Lucide -->
<script src="/js/animations.js"></script>

@stack('scripts')
</body>
</html>
