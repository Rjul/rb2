<footer class="mt-5 bg-navy py-14 text-slate-300">
    <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-8 px-6 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
        <div>
            <div class="mb-3 font-display text-2xl text-white">Radio Bastides</div>
            <p class="max-w-[34ch] text-sm leading-relaxed">
                La radio associative de Villeneuve-sur-Lot et du Lot-et-Garonne.
                Culture, société et musique, au plus près du territoire.
            </p>
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
            <a href="/l-association" class="block py-1 text-sm transition hover:text-green-l">L'association</a>
            <a href="/informations-generales" class="block py-1 text-sm transition hover:text-green-l">Informations</a>
            <a href="/protection-des-donnees" class="block py-1 text-sm transition hover:text-green-l">Protection des données</a>
        </div>
        <div>
            <h4 class="mb-3 font-display text-lg text-white">Suivre</h4>
            <a href="https://www.facebook.com/radiobastides" target="_blank" rel="noopener" class="block py-1 text-sm transition hover:text-green-l">Facebook</a>
            <a href="https://www.instagram.com/radiobastides/" target="_blank" rel="noopener" class="block py-1 text-sm transition hover:text-green-l">Instagram</a>
            <a href="mailto:contactez-nous@mediascitoyens.fr" class="block py-1 text-sm transition hover:text-green-l">Nous écrire</a>
        </div>
    </div>
</footer>
