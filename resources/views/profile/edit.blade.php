<x-guest-layout title="Mon compte">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-[26px] leading-tight text-navy">Mon compte</h1>
            <p class="mt-0.5 text-sm text-ink/70">{{ $user->name }} · {{ $user->email }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="shrink-0 rounded-xl border border-line px-4 py-2 text-sm font-semibold text-ink transition hover:bg-line/40">
                Se déconnecter
            </button>
        </form>
    </div>

    {{-- Informations du compte --}}
    <section class="mt-8 border-t border-line pt-7">
        <h2 class="font-display text-[19px] text-navy">Mes informations</h2>
        @if (session('status') === 'profile-updated')
            <p class="mt-2 rounded-xl bg-green/10 px-4 py-2.5 text-sm font-medium text-green-d">Informations mises à jour.</p>
        @endif
        <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-4">
            @csrf
            @method('patch')
            <div>
                <label for="name" class="block text-sm font-semibold text-ink">Nom</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                       class="mt-1.5 block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
                @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-ink">Adresse e-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                       class="mt-1.5 block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
                @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-xl bg-green px-5 py-3 font-display text-white transition hover:bg-green-d">Enregistrer</button>
        </form>
    </section>

    {{-- Changer le mot de passe --}}
    <section class="mt-8 border-t border-line pt-7">
        <h2 class="font-display text-[19px] text-navy">Changer le mot de passe</h2>
        @if (session('status') === 'password-updated')
            <p class="mt-2 rounded-xl bg-green/10 px-4 py-2.5 text-sm font-medium text-green-d">Mot de passe mis à jour.</p>
        @endif
        <form method="POST" action="{{ route('password.update') }}" class="mt-4 space-y-4">
            @csrf
            @method('put')
            <div>
                <label for="current_password" class="block text-sm font-semibold text-ink">Mot de passe actuel</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                       class="mt-1.5 block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
                @error('current_password', 'updatePassword') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-ink">Nouveau mot de passe</label>
                <input id="password" name="password" type="password" autocomplete="new-password"
                       class="mt-1.5 block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
                @error('password', 'updatePassword') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-ink">Confirmer le nouveau mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="mt-1.5 block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
                @error('password_confirmation', 'updatePassword') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-xl bg-green px-5 py-3 font-display text-white transition hover:bg-green-d">Mettre à jour</button>
        </form>
    </section>

    {{-- RGPD : suppression définitive du compte (<details> natif, sans JS) --}}
    <section class="mt-8 border-t border-line pt-7">
        <h2 class="font-display text-[19px] text-red-700">Supprimer mon compte</h2>
        <p class="mt-2 text-sm text-ink/70">
            Conformément au RGPD, vous pouvez supprimer définitivement votre compte et les données associées
            (dont vos commentaires). Cette action est <strong>irréversible</strong>.
        </p>

        <details class="mt-4 rounded-xl bg-red-50 p-4" {{ $errors->userDeletion->isNotEmpty() ? 'open' : '' }}>
            <summary class="cursor-pointer font-semibold text-red-700">Je souhaite supprimer mon compte</summary>
            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-3">
                @csrf
                @method('delete')
                <label for="password_deletion" class="block text-sm font-semibold text-ink">Confirmez avec votre mot de passe</label>
                <input id="password_deletion" name="password" type="password" autocomplete="current-password"
                       class="block w-full rounded-xl border border-line px-4 py-3 text-ink focus:border-red-500 focus:ring-2 focus:ring-red-500/30">
                @error('password', 'userDeletion') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 font-display text-white transition hover:bg-red-700">
                    Supprimer définitivement mon compte
                </button>
            </form>
        </details>
    </section>
</x-guest-layout>
