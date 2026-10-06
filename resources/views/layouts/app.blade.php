<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'BookManager')</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "tertiary": "#272b2d", "secondary": "#0058be",
                    "surface-container-high": "#dee8ff", "surface-dim": "#cfdaf2",
                    "on-secondary-container": "#fefcff", "on-surface": "#111c2d",
                    "surface-variant": "#d8e3fb", "on-tertiary-container": "#aaadaf",
                    "secondary-fixed-dim": "#adc6ff", "on-secondary": "#ffffff",
                    "on-error": "#ffffff", "on-tertiary": "#ffffff",
                    "inverse-on-surface": "#ecf1ff", "inverse-surface": "#263143",
                    "on-primary-fixed": "#00164e", "tertiary-fixed": "#e0e3e5",
                    "on-primary-container": "#90a8ff", "surface-container-low": "#f0f3ff",
                    "tertiary-fixed-dim": "#c4c7c9", "on-error-container": "#93000a",
                    "on-surface-variant": "#444651", "surface-container-highest": "#d8e3fb",
                    "on-background": "#111c2d", "on-tertiary-fixed-variant": "#444749",
                    "outline": "#757682", "on-secondary-fixed-variant": "#004395",
                    "on-secondary-fixed": "#001a42", "outline-variant": "#c5c5d3",
                    "on-primary-fixed-variant": "#264191", "primary-fixed-dim": "#b6c4ff",
                    "inverse-primary": "#b6c4ff", "surface-container-lowest": "#ffffff",
                    "secondary-fixed": "#d8e2ff", "surface-tint": "#4059aa",
                    "error-container": "#ffdad6", "tertiary-container": "#3d4143",
                    "surface": "#f9f9ff", "secondary-container": "#2170e4",
                    "surface-container": "#e7eeff", "surface-bright": "#f9f9ff",
                    "primary": "#00236f", "primary-fixed": "#dce1ff",
                    "error": "#ba1a1a", "primary-container": "#1e3a8a",
                    "on-primary": "#ffffff", "background": "#f9f9ff",
                    "on-tertiary-fixed": "#191c1e",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: {
                    xl: "32px", "2xl": "48px", "margin-mobile": "16px", sm: "8px",
                    md: "16px", xs: "4px", "3xl": "64px", gutter: "24px",
                    lg: "24px", base: "4px", "container-max": "1280px",
                },
                fontFamily: {
                    "headline-lg": ["Inter"], "headline-lg-mobile": ["Inter"], "body-lg": ["Inter"],
                    "headline-md": ["Inter"], "label-md": ["Inter"], "body-md": ["Inter"],
                    "label-sm": ["Inter"], "display-lg": ["Inter"],
                },
                fontSize: {
                    "headline-lg": ["32px", { lineHeight: "40px", letterSpacing: "-0.01em", fontWeight: "600" }],
                    "headline-lg-mobile": ["24px", { lineHeight: "32px", fontWeight: "600" }],
                    "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                    "headline-md": ["24px", { lineHeight: "32px", fontWeight: "600" }],
                    "label-md": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "500" }],
                    "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                    "label-sm": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                    "display-lg": ["48px", { lineHeight: "56px", letterSpacing: "-0.02em", fontWeight: "700" }],
                },
            },
        },
    };
</script>

<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .icon-fill { font-variation-settings: 'FILL' 1; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background-color: #c5c5d3; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background-color: #757682; }
</style>
@stack('styles')
</head>
<body class="bg-background text-on-background font-body-md min-h-screen" x-data="{ mobileMenuOpen: false }">

@include('partials.sidebar')

<div class="flex-1 flex flex-col min-w-0 md:ml-64 relative bg-background">
    @include('partials.topbar')

    <main class="flex-1 overflow-y-auto p-lg lg:p-xl pb-24 md:pb-xl">
        <div class="max-w-container-max mx-auto space-y-xl">

            @if (session('success'))
                <div class="bg-secondary-container/20 border border-secondary text-on-secondary-fixed px-lg py-md rounded-lg font-body-md text-body-md flex items-center gap-sm">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-error-container/40 border border-error-container text-on-error-container px-lg py-md rounded-lg font-body-md text-body-md flex items-center gap-sm">
                    <span class="material-symbols-outlined">warning</span>
                    {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-error-container/40 border border-error-container text-on-error-container px-lg py-md rounded-lg font-body-md text-body-md">
                    <p class="font-bold mb-xs">Merci de corriger les champs suivants :</p>
                    <ul class="list-disc list-inside">
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

@include('partials.bottomnav')

@stack('scripts')
</body>
</html>
