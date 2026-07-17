<x-guest-layout title="Nouveau mot de passe">
    <h1 class="font-display text-[26px] leading-tight text-navy">Nouveau mot de passe</h1>
    <p class="mt-2 text-sm text-ink/70">Choisissez un nouveau mot de passe pour votre compte.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-sm font-semibold text-ink">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autocomplete="username"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-ink">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required autofocus autocomplete="new-password"
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
            Réinitialiser le mot de passe
        </button>
    </form>
</x-guest-layout>
