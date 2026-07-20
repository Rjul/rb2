<x-guest-layout title="Créer un compte">
    <h1 class="font-display text-[28px] leading-tight text-navy">Créer un compte</h1>
    <p class="mt-1 text-sm text-ink/70">Rejoignez Radio Bastides pour commenter les émissions.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
        @csrf

        {{-- Anti-bot (honeypot) : hors écran pour les humains, souvent rempli par les bots.
             S'il est rempli → inscription ignorée sans envoi d'email. --}}
        <div aria-hidden="true" style="position:absolute;left:-9999px;height:0;width:0;overflow:hidden;" tabindex="-1">
            <label for="website">Laissez ce champ vide</label>
            <input id="website" name="website" type="text" value="" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <label for="name" class="block text-sm font-semibold text-ink">Nom</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-ink">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-ink">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-ink">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('password_confirmation') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Créer mon compte
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink/70">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="font-semibold text-green transition hover:text-green-d">Se connecter</a>
    </p>
</x-guest-layout>
