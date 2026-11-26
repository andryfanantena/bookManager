@extends('layouts.app')

@section('title', 'BookManager - Mon Profil & Photo')

@section('content')

<div class="space-y-6 sm:space-y-8" x-data="{
    avatarPreview: null,
    onAvatarChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.avatarPreview = e.target.result;
                if (window.gsap) {
                    gsap.from('#profile-avatar-img', { scale: 0.88, duration: 0.4, ease: 'back.out(2)' });
                }
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-outline-variant/60">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-secondary uppercase tracking-wider mb-1">
                <i data-lucide="user-cog" class="w-4 h-4" aria-hidden="true"></i>
                <span>Paramètres du compte</span>
            </div>
            <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-on-surface">Mon Profil</h2>
            <p class="font-body-md text-sm sm:text-base text-on-surface-variant mt-1">Gérez vos informations personnelles et mettez à jour votre photo de profil.</p>
        </div>

        <!-- Bouton Déconnexion rapide -->
        <div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50/50 hover:bg-rose-100/60 transition-all font-semibold text-xs sm:text-sm shadow-2xs hover:shadow hover:-translate-y-0.5 active:translate-y-0"
                >
                    <i data-lucide="log-out" class="w-4 h-4 text-rose-600" aria-hidden="true"></i>
                    <span>Se déconnecter</span>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Carte Résumé Profil & Avatar -->
        <div class="lg:col-span-4 bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col items-center text-center space-y-4">
            <!-- Zone interactive Avatar -->
            <div class="relative group">
                <div id="profile-avatar-img" class="w-32 h-32 rounded-full overflow-hidden border-4 border-surface-container-high shadow-md bg-surface-container flex items-center justify-center">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Nouvel avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!avatarPreview">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </template>
                </div>
                <!-- Pastille bouton camera -->
                <label for="avatar_input" class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center shadow-md hover:bg-primary-container cursor-pointer transition-transform hover:scale-110 active:scale-95" title="Modifier la photo" aria-label="Modifier la photo de profil">
                    <i data-lucide="camera" class="w-4 h-4" aria-hidden="true"></i>
                </label>
            </div>

            <div class="space-y-1">
                <h3 class="font-headline-md text-lg font-bold text-on-surface">{{ $user->name }}</h3>
                <p class="text-xs text-on-surface-variant font-mono">{{ $user->email }}</p>
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container/15 text-secondary text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span> Membre depuis {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}
                    </span>
                </div>
            </div>

            <!-- Bouton pour supprimer l'avatar existant si présent -->
            @if ($user->avatar)
                <div class="w-full pt-2 border-t border-outline-variant/60">
                    <form action="{{ route('profile.avatar.destroy') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer votre photo de profil ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-3 text-xs text-rose-700 hover:bg-rose-50 rounded-xl font-medium transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600" aria-hidden="true"></i>
                            <span>Supprimer la photo actuelle</span>
                        </button>
                    </form>
                </div>
            @endif

            <div class="w-full bg-surface-container p-4 rounded-xl text-left space-y-1.5 text-xs text-on-surface-variant">
                <p class="font-semibold text-on-surface flex items-center gap-1.5">
                    <i data-lucide="info" class="w-4 h-4 text-secondary flex-shrink-0" aria-hidden="true"></i>
                    <span>Conseil photo :</span>
                </p>
                <p>Pour un résultat optimal, utilisez une image carrée (format PNG, JPG ou WEBP) de 500x500 px minimum (max 2 Mo).</p>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div class="lg:col-span-8 space-y-6">

            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-7 shadow-xs">
                <h3 class="font-headline-md text-base sm:text-lg text-on-surface font-semibold mb-1">Informations du compte &amp; Photo</h3>
                <p class="text-xs sm:text-sm text-on-surface-variant mb-6">Mettez à jour votre nom, adresse email ou sélectionnez une nouvelle photo de profil.</p>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Input pour changer la photo de profil -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-[20px]">
                        <!-- Nom -->
                        <div class="space-y-1">
                            <label for="name" class="block text-xs font-semibold text-on-surface">Nom complet</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline">
                                    <i data-lucide="user" class="w-4 h-4" aria-hidden="true"></i>
                                </div>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    autocomplete="name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                                >
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label for="email" class="block text-xs font-semibold text-on-surface">Adresse Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline">
                                    <i data-lucide="mail" class="w-4 h-4" aria-hidden="true"></i>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    autocomplete="email"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary transition-all shadow-2xs"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Séparateur Sécurité -->
                    <div class="pt-5 border-t border-outline-variant/60">
                        <h4 class="font-semibold text-xs sm:text-sm text-on-surface flex items-center gap-2 mb-1">
                            <i data-lucide="lock" class="w-4 h-4 text-secondary" aria-hidden="true"></i>
                            <span>Modifier le mot de passe (Facultatif)</span>
                        </h4>
                        <p class="text-xs text-on-surface-variant mb-4">Laissez vide si vous ne souhaitez pas changer votre mot de passe actuel.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label for="current_password" class="block text-xs font-medium text-on-surface-variant">Mot de passe actuel</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    placeholder="••••••••"
                                    class="w-full px-3.5 py-2 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary shadow-2xs"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="password" class="block text-xs font-medium text-on-surface-variant">Nouveau mot de passe</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="••••••••"
                                    class="w-full px-3.5 py-2 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary shadow-2xs"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="password_confirmation" class="block text-xs font-medium text-on-surface-variant">Confirmer nouveau</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    placeholder="••••••••"
                                    class="w-full px-3.5 py-2 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary shadow-2xs"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container text-xs sm:text-sm font-medium transition-colors">
                            Annuler
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0"
                        >
                            <i data-lucide="save" class="w-4 h-4" aria-hidden="true"></i>
                            <span>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

@endsection
