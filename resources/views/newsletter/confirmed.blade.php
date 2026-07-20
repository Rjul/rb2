<x-guest-layout title="Inscription confirmée">
    <div class="text-center">
        <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-full bg-green-l/20 text-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-7 w-7"><path d="m5 13 4 4L19 7"/></svg>
        </div>
        <h1 class="font-display text-[26px] leading-tight text-navy">Inscription confirmée&nbsp;!</h1>
        <p class="mt-2 text-sm text-ink/70">
            <span class="font-semibold text-ink">{{ $email }}</span> est bien inscrit à la newsletter de Radio Bastides.
            Rendez-vous chaque vendredi pour la sélection de la semaine.
        </p>
        <a href="{{ route('v2.home') }}" wire:navigate
           class="mt-6 inline-flex rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green-d">
            Retour à l'accueil
        </a>
    </div>
</x-guest-layout>
