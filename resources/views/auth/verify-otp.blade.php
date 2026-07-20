<x-guest-layout title="Vérifier votre email">
    <h1 class="font-display text-[28px] leading-tight text-navy">Vérifiez votre email</h1>
    <p class="mt-1 text-sm text-ink/70">
        Nous avons envoyé un code à <span class="font-semibold text-ink">{{ auth()->user()->email }}</span>.
        Saisissez-le ci-dessous pour activer votre compte.
    </p>

    @if (session('status') === 'otp-resent')
        <div class="mt-4 rounded-xl border border-green-l/50 bg-green-l/10 px-4 py-3 text-sm text-green-d">
            Un nouveau code vient de vous être envoyé.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="code" class="block text-sm font-semibold text-ink">Code de vérification</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                   maxlength="6" pattern="[0-9]*" required autofocus placeholder="123456"
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-4 py-3 text-center font-display text-2xl tracking-[0.4em] text-ink focus:border-green focus:ring-2 focus:ring-green/30">
            @error('code') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Valider mon compte
        </button>
    </form>

    <div class="mt-6 flex items-center justify-between text-sm">
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="font-semibold text-green transition hover:text-green-d">Renvoyer le code</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-ink/60 transition hover:text-ink">Se déconnecter</button>
        </form>
    </div>
</x-guest-layout>
