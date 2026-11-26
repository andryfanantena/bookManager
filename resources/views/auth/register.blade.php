@extends('layouts.auth')

@section('title', 'Inscription - BookManager')

@section('content')
<div class="auth-card max-w-4xl mx-auto bg-surface-container-lowest rounded-3xl shadow-xl shadow-slate-200/60 border border-outline-variant/70 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[600px]">

    <!-- Colonne latérale de présentation (Branding & Visuals) -->
    <div class="lg:col-span-5 bg-gradient-to-br from-primary via-primary-container to-secondary p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden">
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
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    Création de compte
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-snug">
                    Rejoignez BookManager en quelques clics.
                </h2>
                <p class="text-xs sm:text-sm text-blue-100/90 text-justify leading-relaxed">
                    Créez votre profil, importez ou scannez vos ouvrages et profitez d'outils statistiques et d'export puissants.
                </p>
            </div>
        </div>

        <div id="register-floating-badge" class="relative z-10 mt-8 bg-white/10 backdrop-blur-md rounded-xl p-3.5 border border-white/15 shadow-lg space-y-1.5">
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-100">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-300" aria-hidden="true"></i>
                <span>Données protégées &amp; sécurisées</span>
            </div>
            <p class="text-[11px] text-white/80">Gestion multi-catégories, export PDF et export Excel inclus sans limite.</p>
        </div>
    </div>

    <!-- Colonne du Formulaire d'inscription -->
    <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center" x-data="{
        avatarPreview: null,
        showPassword: false,
        onAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                    if (window.gsap) {
                        gsap.from('#avatar-circle', { scale: 0.85, duration: 0.4, ease: 'back.out(2)' });
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    }">
        <div class="max-w-md w-full mx-auto space-y-4">

            <div class="auth-stagger">
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Créer un nouveau compte</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-1">Renseignez vos coordonnées pour démarrer immédiatement.</p>
            </div>

            <!-- Messages d'erreur globaux -->
            @if ($errors->any())
                <div class="auth-stagger p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1 text-rose-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0" aria-hidden="true"></i>
                        <span>Veuillez corriger les erreurs ci-dessous</span>
                    </div>
                    <ul class="text-xs space-y-0.5 list-disc list-inside ml-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <!-- Champ Nom complet -->
                <div class="auth-stagger pt-5 space-y-1">
                    <label for="name" class="block text-xs font-semibold text-on-surface uppercase tracking-wider">
                        Nom complet
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                            <i data-lucide="user" class="w-4 h-4" aria-hidden="true"></i>
                        </div>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                            placeholder="John Doe"
                            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                        >
                    </div>
                </div>

                <!-- Champ Email -->
                <div class="auth-stagger pt-5 space-y-1">
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
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="nom@exemple.com"
                            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                        >
                    </div>
                </div>

                <!-- Champs Mot de passe & Confirmation (Grille 2 colonnes) -->
                <div class="auth-stagger pt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-semibold text-on-surface uppercase tracking-wider">
                            Mot de passe
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                                <i data-lucide="lock" class="w-3.5 h-3.5" aria-hidden="true"></i>
                            </div>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                placeholder="6+ caract."
                                class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-xs font-semibold text-on-surface uppercase tracking-wider">
                            Confirmation
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-outline group-focus-within:text-secondary transition-colors">
                                <i data-lucide="lock" class="w-3.5 h-3.5" aria-hidden="true"></i>
                            </div>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Répéter..."
                                class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                            >
                        </div>
                    </div>
                </div>

                <!-- Bascule voir les mots de passe -->
                <div class="auth-stagger flex items-center justify-between text-xs text-on-surface-variant">
                    <button type="button" @click="showPassword = !showPassword" class="flex items-center gap-1.5 text-outline hover:text-on-surface transition-colors">
                        <i data-lucide="eye" class="w-4 h-4" aria-hidden="true" x-show="!showPassword"></i>
                        <i data-lucide="eye-off" class="w-4 h-4" aria-hidden="true" x-show="showPassword" x-cloak></i>
                        <span x-text="showPassword ? 'Masquer les mots de passe' : 'Afficher les mots de passe'"></span>
                    </button>
                    <span>Min. 6 caractères</span>
                </div>

                <!-- Bouton Créer le compte -->
                <div class="auth-stagger pt-5">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-container text-white py-2.5 px-4 rounded-xl font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <span>Créer mon compte</span>
                        <i data-lucide="user-plus" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

            <!-- Lien vers Connexion -->
            <div class="auth-stagger pt-3 text-center border-t border-outline-variant/60">
                <p class="text-xs text-on-surface-variant">
                    Vous avez déjà un compte ?
                    <a href="{{ route('login') }}" class="font-semibold text-secondary hover:text-primary transition-colors hover:underline">
                        Se connecter
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
            gsap.to("#register-floating-badge", {
                y: -5,
                duration: 2.8,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
        }
    });
</script>
@endpush
