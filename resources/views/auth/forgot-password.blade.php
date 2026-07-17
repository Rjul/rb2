<x-guest-layout title="Mot de passe oublié">
    <h1 class="font-display text-[26px] leading-tight text-navy">Mot de passe oublié</h1>
    <p class="mt-2 text-sm text-ink/70">
        Indiquez votre adresse e-mail : nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-xl bg-green/10 px-4 py-3 text-sm font-medium text-green-d">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-semibold text-ink">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="w-full rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Envoyer le lien
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink/70">
        <a href="{{ route('login') }}" class="font-semibold text-green transition hover:text-green-d">← Retour à la connexion</a>
    </p>
</x-guest-layout>
