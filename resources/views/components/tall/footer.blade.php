<footer class="relative mt-5 overflow-hidden bg-navy py-14 text-slate-300">
    {{-- Ondes décoratives reprises de l'ancien footer (public/imgs, fichier legacy) --}}
    <img src="/imgs/bastides_onde_footer.svg" alt="" aria-hidden="true" loading="lazy" width="1080" height="1080"
         class="pointer-events-none absolute left-3 top-1/2 hidden h-44 w-auto -translate-y-1/2 opacity-25 select-none motion-safe:animate-onde sm:block">
    <img src="/imgs/bastides_onde_footer.svg" alt="" aria-hidden="true" loading="lazy" width="1080" height="1080"
         class="pointer-events-none absolute right-3 top-1/2 hidden h-44 w-auto -translate-y-1/2 rotate-180 opacity-25 select-none motion-safe:animate-onde sm:block"
         style="animation-delay: -2.5s">

    <div class="relative mx-auto grid max-w-[1200px] grid-cols-1 gap-8 px-6 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
        <div>
            <a href="{{ route('v2.home') }}" wire:navigate class="inline-block" aria-label="Radio Bastides — accueil">
                <img src="/storage/img/blanco-peqe.png" alt="Radio Bastides" width="475" height="116" class="h-10 w-auto">
            </a>
            <p class="mt-3 text-sm leading-relaxed text-green-l">La radio de Médias Citoyens en Villeneuvois.</p>
            <p class="mt-1 max-w-[34ch] text-sm leading-relaxed">Culture, société et musique, au plus près du territoire.</p>
            <a href="https://mediascitoyens.fr/" target="_blank" rel="noopener" class="mt-5 inline-block opacity-90 transition hover:opacity-100">
                <img src="/storage/img/media-citoyens-blanco.png" alt="Médias Citoyens en Villeneuvois" width="395" height="162" class="h-16 w-auto">
            </a>
        </div>
        <div>
            <h4 class="mb-3 font-display text-lg text-white">Explorer</h4>
            <a href="{{ route('v2.categories') }}" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Catégories</a>
            <a href="{{ route('v2.programmes') }}" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Programmes</a>
            <a href="{{ route('v2.emissions') }}" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Émissions</a>
            <a href="{{ route('v2.themes') }}" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Thèmes</a>
        </div>
        <div>
            <h4 class="mb-3 font-display text-lg text-white">Le média</h4>
            <a href="/l-association" wire:navigate class="block py-1 text-sm transition hover:text-green-l">L'association</a>
            <a href="/responsabilité-legale" wire:navigate class="block py-1 text-sm transition hover:text-green-l">La responsabilité légale</a>
            <a href="{{ route('informations') }}" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Informations</a>
            <a href="/protection-des-donnees" wire:navigate class="block py-1 text-sm transition hover:text-green-l">Protection des données</a>
        </div>
        <div>
            <h4 class="mb-3 font-display text-lg text-white">Suivre</h4>
            <div class="flex gap-3">
                <a href="https://www.facebook.com/radiobastides" target="_blank" rel="noopener" aria-label="Radio Bastides sur Facebook"
                   class="grid h-11 w-11 place-items-center rounded-full border border-white/30 text-white transition hover:border-green-l hover:text-green-l">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true"><path d="M13.4 21v-6.9h2.3l.44-3h-2.74V9.2c0-.87.25-1.46 1.5-1.46h1.35V5.1c-.28-.04-1.1-.12-2.05-.12-2.03 0-3.42 1.24-3.42 3.52v1.6H8.5v3h2.28V21h2.62Z"/></svg>
                </a>
                <a href="https://www.instagram.com/radiobastides/" target="_blank" rel="noopener" aria-label="Radio Bastides sur Instagram"
                   class="grid h-11 w-11 place-items-center rounded-full border border-white/30 text-white transition hover:border-green-l hover:text-green-l">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.6" fill="currentColor" stroke="none"/></svg>
                </a>
            </div>
            <p class="mt-5 text-sm text-green-l">Vous pouvez nous écrire à</p>
            <a href="mailto:contactez-nous@mediascitoyens.fr" class="mt-1 block break-all text-sm text-white transition hover:text-green-l">contactez-nous@mediascitoyens.fr</a>
        </div>
    </div>
</footer>
