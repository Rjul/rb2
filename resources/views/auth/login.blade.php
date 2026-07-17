<x-guest-layout title="Connexion">
    <h1 class="font-display text-[28px] leading-tight text-navy">Connexion</h1>
    <p class="mt-1 text-sm text-ink/70">Connectez-vous pour commenter les émissions.</p>

    {{-- Message de statut (ex. lien de réinitialisation envoyé) --}}
    @if (session('status'))
        <div class="mt-4 rounded-xl bg-green/10 px-4 py-3 text-sm font-medium text-green-d">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-ink">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink placeholder:text-ink/40 focus:border-green focus:ring-2 focus:ring-green/30">
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-ink">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-green transition hover:text-green-d">Mot de passe oublié ?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2.5 text-sm text-ink/80">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line text-green focus:ring-green/40">
            Se souvenir de moi
        </label>

        <button type="submit"
                class="w-full rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Se connecter
        </button>
    </form>

    {{-- Connexions sociales (routes Socialite existantes) --}}
    @if (Route::has('login.google') || Route::has('login.facebook'))
        <div class="my-6 flex items-center gap-3 text-xs uppercase tracking-wider text-ink/40">
            <span class="h-px flex-1 bg-line"></span>ou<span class="h-px flex-1 bg-line"></span>
        </div>
        <div class="space-y-2.5">
            @if (Route::has('login.google'))
                <a href="{{ route('login.google') }}"
                   class="flex w-full items-center justify-center gap-2.5 rounded-xl border border-line px-4 py-3 text-sm font-semibold text-ink transition hover:bg-line/40">
                    Continuer avec Google
                </a>
            @endif
            @if (Route::has('login.facebook'))
                <a href="{{ route('login.facebook') }}"
                   class="flex w-full items-center justify-center gap-2.5 rounded-xl border border-line px-4 py-3 text-sm font-semibold text-ink transition hover:bg-line/40">
                    Continuer avec Facebook
                </a>
            @endif
        </div>
    @endif

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-ink/70">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="font-semibold text-green transition hover:text-green-d">Créer un compte</a>
        </p>
    @endif
</x-guest-layout>
