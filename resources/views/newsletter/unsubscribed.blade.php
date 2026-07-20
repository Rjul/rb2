<x-guest-layout title="Désinscription">
    <div class="text-center">
        <h1 class="font-display text-[26px] leading-tight text-navy">Vous êtes désinscrit</h1>
        <p class="mt-2 text-sm text-ink/70">
            <span class="font-semibold text-ink">{{ $email }}</span> ne recevra plus la newsletter de Radio Bastides.
            Vous pouvez vous réinscrire à tout moment depuis le site.
        </p>
        <a href="{{ route('v2.home') }}" wire:navigate
           class="mt-6 inline-flex rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Retour à l'accueil
        </a>
    </div>
</x-guest-layout>
