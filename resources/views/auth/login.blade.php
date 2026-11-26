@extends('layouts.auth')

@section('title', 'Connexion - BookManager')

@section('content')
<div class="auth-card max-w-4xl mx-auto bg-surface-container-lowest rounded-3xl shadow-xl shadow-slate-200/60 border border-outline-variant/70 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[560px]">

    <!-- Colonne latérale de présentation (Branding & Visuals) -->
    <div class="lg:col-span-5 bg-gradient-to-br from-primary via-primary-container to-secondary p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden">
        <!-- Décorations de fond -->
        <div class="absolute -right-16 -top-16 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-56 h-56 bg-secondary-container/30 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center text-white shadow-inner">
                    <i data-lucide="book-open" class="w-5 h-5 text-white" aria-hidden="true"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-white leading-tight">BookManager</h1>
                    <span class="text-[10px] text-blue-200 font-medium tracking-wide uppercase">Gestion de Bibliothèque</span>
                </div>
            </div>

            <div class="mt-10 space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm text-xs font-semibold tracking-wide text-blue-100 border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Connexion au compte
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-snug">
                    Organisez votre collection littéraire avec élégance.
                </h2>
                <p class="text-xs sm:text-sm text-blue-100/90 text-justify leading-relaxed">
                    Scannez vos codes ISBN, enrichissez vos fiches via la Bibliothèque nationale de France et pilotez votre catalogue en toute fluidité.
                </p>
            </div>
        </div>

        <!-- Mini carte statistique animée avec GSAP -->
        <div id="hero-floating-card" class="relative z-10 mt-8 bg-white/10 backdrop-blur-md rounded-xl p-3.5 border border-white/15 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="badge-check" class="w-5 h-5" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-[11px] text-blue-100 font-medium">Reconnaissance IA &amp; BnF</p>
                    <p class="text-xs font-semibold text-white">Métadonnées automatisées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne du Formulaire de connexion -->
    <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center" x-data="{
        email: '{{ old('email', '') }}',
        password: '',
        showPassword: false,
        fillDemo() {
            this.email = 'test@example.com';
            this.password = 'password';
            if (window.gsap) {
                gsap.from('#login-form-container', { scale: 0.99, duration: 0.3, ease: 'power2.out' });
            }
        }
    }">
        <div id="login-form-container" class="max-w-md w-full mx-auto space-y-5">

            <div class="auth-stagger">
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Bon retour parmi nous !</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-1">Connectez-vous pour accéder à votre catalogue et vos statistiques.</p>
            </div>

            <!-- Messages d'alerte -->
            @if (session('success'))
                <div class="auth-stagger p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs sm:text-sm flex items-center gap-2.5">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-stagger p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1 text-rose-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0" aria-hidden="true"></i>
                        <span>Identifiants invalides</span>
                    </div>
                    <ul class="text-xs space-y-0.5 list-disc list-inside ml-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Champ Email -->
                <div class="auth-stagger space-y-1">
                    <label for="email" class="block text-xs font-semibold text-on-surface uppercase tracking-wider">
                        Adresse Email
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                            <i data-lucide="mail" class="w-4 h-4" aria-hidden="true"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            x-model="email"
                            required
                            autocomplete="email"
                            placeholder="nom@exemple.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                        >
                    </div>
                </div>

                <!-- Champ Mot de passe -->
                <div class="auth-stagger pt-5 space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-semibold text-on-surface uppercase tracking-wider">
                            Mot de passe
                        </label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                            <i data-lucide="lock" class="w-4 h-4" aria-hidden="true"></i>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            x-model="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            aria-label="Afficher ou masquer le mot de passe"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-outline hover:text-on-surface transition-colors focus:outline-none"
                        >
                            <i data-lucide="eye" class="w-4 h-4" aria-hidden="true" x-show="!showPassword"></i>
                            <i data-lucide="eye-off" class="w-4 h-4" aria-hidden="true" x-show="showPassword" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Se souvenir de moi -->
                <div class="auth-stagger flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 rounded text-secondary focus:ring-secondary border-outline-variant"
                        >
                        <span class="text-xs text-on-surface-variant font-medium">Se souvenir de moi</span>
                    </label>
                </div>

                <!-- Bouton de Soumission -->
                <div class="auth-stagger pt-1">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-container text-white py-2.5 px-4 rounded-xl font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <span>Se connecter</span>
                        <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

            <!-- Lien vers Inscription -->
            <div class="auth-stagger pt-3 text-center border-t border-outline-variant/60">
                <p class="text-xs text-on-surface-variant">
                    Vous n'avez pas encore de compte ?
                    <a href="{{ route('register') }}" class="font-semibold text-secondary hover:text-primary transition-colors hover:underline">
                        Créer un compte
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.gsap) {
            gsap.to("#hero-floating-card", {
                y: -5,
                duration: 2.5,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
        }
    });
</script>
@endpush
