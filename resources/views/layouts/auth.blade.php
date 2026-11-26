<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentification - BookManager')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vector Icons - Lucide (conforme /icon-design, aucun émoji) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- GSAP Core & Plugins (conforme /gsap-timeline /gsap-performance) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00236f",
                        "primary-container": "#1e3a8a",
                        "on-primary": "#ffffff",
                        "secondary": "#0058be",
                        "secondary-container": "#2170e4",
                        "on-secondary-container": "#fefcff",
                        "surface": "#f9f9ff",
                        "surface-container": "#eef2ff",
                        "surface-container-high": "#dee8ff",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#0f172a",
                        "on-surface-variant": "#475569",
                        "outline": "#94a3b8",
                        "outline-variant": "#e2e8f0",
                        "error": "#dc2626",
                        "error-container": "#fee2e2",
                        "on-error-container": "#991b1b",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .book-spine-pattern {
            background-image: radial-gradient(rgba(0, 88, 190, 0.07) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 text-on-surface antialiased flex flex-col justify-center relative overflow-x-hidden book-spine-pattern">

    <!-- Orbes d'ambiance avec GPU will-change (/gsap-performance) -->
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden" aria-hidden="true">
        <div id="orb-1" class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl will-change-transform"></div>
        <div id="orb-2" class="absolute top-1/2 -right-32 w-[32rem] h-[32rem] bg-indigo-100/50 rounded-full blur-3xl will-change-transform"></div>
        <div id="orb-3" class="absolute -bottom-32 left-1/3 w-80 h-80 bg-sky-200/30 rounded-full blur-2xl will-change-transform"></div>
    </div>

    <!-- Contenu principal -->
    <div class="w-full max-w-6xl mx-auto p-4 sm:p-6 lg:p-8">
        @yield('content')
    </div>

    <footer class="py-6 text-center text-xs text-on-surface-variant/80">
        <div class="flex items-center justify-center gap-2">
            <i data-lucide="book-open" class="w-4 h-4 text-primary" aria-hidden="true"></i>
            <span class="font-semibold text-primary">BookManager</span> &copy; {{ date('Y') }} — Gestion intelligente de bibliothèque &amp; catalogage BnF
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialiser les icônes Lucide
            if (window.lucide) {
                window.lucide.createIcons({
                    attrs: { 'stroke-width': 1.75 }
                });
            }

            if (window.gsap) {
                const mm = gsap.matchMedia();

                mm.add("(prefers-reduced-motion: no-preference)", () => {
                    // Ambiance fluide des orbes (gsap-performance : transforms uniquement)
                    gsap.to("#orb-1", { x: 35, y: 25, duration: 8, repeat: -1, yoyo: true, ease: "sine.inOut" });
                    gsap.to("#orb-2", { x: -40, y: -30, duration: 10, repeat: -1, yoyo: true, ease: "sine.inOut" });
                    gsap.to("#orb-3", { x: 25, y: -20, duration: 9, repeat: -1, yoyo: true, ease: "sine.inOut" });

                    // Timeline d'entrée auth (gsap-timeline)
                    const authTl = gsap.timeline({ defaults: { ease: "power3.out" } });
                    authTl.from(".auth-card", {
                        opacity: 0,
                        y: 20,
                        scale: 0.98,
                        duration: 0.6,
                        clearProps: "transform,opacity"
                    })
                    .from(".auth-stagger", {
                        opacity: 0,
                        y: 12,
                        duration: 0.4,
                        stagger: 0.07,
                        clearProps: "transform,opacity"
                    }, "-=0.3");
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
