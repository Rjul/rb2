@php
    // Navigation principale → hubs v2. wire:navigate partout pour que le
    // lecteur audio persistant survive à la navigation (SPA Livewire).
    $nav = [
        ['label' => 'Émissions', 'url' => route('v2.emissions')],
        ['label' => 'Thèmes',    'url' => route('v2.themes')],
    ];

    // Données des méga-menus « Catégories » / « Programmes » : catégories actives
    // (triées par poids) avec leurs programmes. Identique pour tous les visiteurs →
    // cache front, invalidé à chaque écriture BO sur GroupProgramme / Programme.
    $megaCategories = \App\Support\FrontCache::remember('nav:mega', fn () => \App\Models\GroupProgramme::query()
        ->where('is_active', 1)
        ->orderBy('height')
        ->orderBy('name') // départage à poids égal : alphabétique
        ->with('programmesOrderByHeightAndActive')
        ->get()
        ->filter(fn ($c) => $c->programmesOrderByHeightAndActive->isNotEmpty())
        ->values());
@endphp

<header x-data="{ open: false, mega: null, mView: 'root', mCat: null, mDir: 'fwd' }"
        x-on:keydown.escape.window="mega = null; open = false"
        x-on:mouseleave="mega = null"
        class="sticky top-0 z-40 border-b border-line bg-bg/95 backdrop-blur">
    <div class="mx-auto flex h-[76px] max-w-[1200px] items-center gap-4 px-6 md:gap-7">
        <a href="{{ route('v2.home') }}" wire:navigate x-on:mouseenter="mega = null"
           class="flex shrink-0 items-center" aria-label="Radio Bastides — accueil">
            <img src="/imgs/logo.png" alt="Radio Bastides" width="221" height="45" class="h-9 w-auto shrink-0 sm:h-10">
        </a>

        <nav class="ml-2 hidden gap-8 md:flex" aria-label="Navigation principale">
            {{-- Catégories : ouvre le méga-menu visuel au survol, reste un lien vers la page --}}
            <a href="{{ route('v2.categories') }}" wire:navigate.hover
               x-on:mouseenter="mega = 'cat'" x-on:focus="mega = 'cat'"
               aria-haspopup="true" :aria-expanded="mega === 'cat' ? 'true' : 'false'"
               class="inline-flex items-center gap-1 font-display text-[19px] text-navy transition hover:text-green"
               :class="mega === 'cat' ? 'text-green' : ''">
                Catégories
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-4 w-4 transition-transform" :class="mega === 'cat' ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
            </a>

            {{-- Programmes : ouvre l'index par catégorie au survol, reste un lien vers la page --}}
            <a href="{{ route('v2.programmes') }}" wire:navigate.hover
               x-on:mouseenter="mega = 'prog'" x-on:focus="mega = 'prog'"
               aria-haspopup="true" :aria-expanded="mega === 'prog' ? 'true' : 'false'"
               class="inline-flex items-center gap-1 font-display text-[19px] text-navy transition hover:text-green"
               :class="mega === 'prog' ? 'text-green' : ''">
                Programmes
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-4 w-4 transition-transform" :class="mega === 'prog' ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
            </a>

            @foreach ($nav as $item)
                {{-- .hover : Livewire précharge la page au survol → navigation quasi instantanée --}}
                <a href="{{ $item['url'] }}" wire:navigate.hover x-on:mouseenter="mega = null"
                   class="font-display text-[19px] text-navy transition hover:text-green">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2 sm:gap-3" x-on:mouseenter="mega = null">
            <a href="{{ route('v2.search') }}" wire:navigate aria-label="Rechercher"
               class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </a>
            <a href="{{ route('login') }}" aria-label="Mon compte"
               class="hidden h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green sm:grid">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            </a>
            <button type="button" @click="open = ! open; mView = 'root'; mDir = 'fwd'" aria-label="Menu" :aria-expanded="open" aria-controls="menu-mobile"
                    class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm md:hidden">
                <svg x-show="! open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>

    {{-- ============================================================
         MÉGA-MENUS DESKTOP (pleine largeur, sous la barre).
         display:none inline (et NON x-cloak) : la règle [x-cloak]{display:none!important}
         fausserait la valeur d'affichage mémorisée par x-show. Pas de x-transition
         (elle bloquait x-show dans ce combo Tailwind v4 / Alpine) : fondu en CSS
         (.mega-panel). Les panneaux sont enfants de <header> : survoler le panneau ne
         déclenche pas le mouseleave du header → fermeture propre en quittant tout le header.
         ============================================================ --}}
    @if ($megaCategories->isNotEmpty())
        {{-- ---------- Catégories (visuel, cartes-images) ---------- --}}
        <div x-show="mega === 'cat'" style="display: none;"
             x-on:mouseenter="mega = 'cat'"
             class="mega-panel absolute inset-x-0 top-full border-t border-line bg-white shadow-xl"
             role="region" aria-label="Catégories">
            <div class="mx-auto max-w-[1200px] px-6 py-8">
                <div class="grid grid-cols-2 gap-x-8 gap-y-7 lg:grid-cols-4">
                    @foreach ($megaCategories as $cat)
                        @php $catImg = $cat->image ?: 'https://picsum.photos/seed/rb-cat-' . $cat->id . '/440/260'; @endphp
                        <div>
                            <a href="{{ $cat->canonicalUrl() }}" wire:navigate
                               class="group block overflow-hidden rounded-2xl shadow-sm">
                                <span class="relative block aspect-[16/10]">
                                    <img src="{{ $catImg }}" alt="" loading="lazy" decoding="async"
                                         onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-cat-{{ $cat->id }}/440/260'"
                                         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    <span class="absolute inset-0 bg-gradient-to-t from-navy/90 via-navy/40 to-transparent"></span>
                                    <span class="absolute inset-x-3.5 bottom-2.5 font-display text-[20px] leading-tight text-white [text-shadow:0_1px_3px_rgba(0,28,65,0.6)]">{{ $cat->name }}</span>
                                </span>
                            </a>

                            <ul class="mt-3.5 space-y-1.5">
                                @foreach ($cat->programmesOrderByHeightAndActive->take(3) as $prog)
                                    <li>
                                        <a href="{{ $prog->canonicalUrl() }}" wire:navigate
                                           class="block truncate text-[15px] text-muted transition hover:text-green">{{ $prog->name }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ $cat->canonicalUrl() }}" wire:navigate
                               class="mt-2.5 inline-flex items-center gap-1 text-sm font-bold text-green transition hover:text-green-d">
                                Voir la catégorie
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-3.5 w-3.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-7 flex items-center justify-between border-t border-line pt-5">
                    <a href="{{ route('v2.categories') }}" wire:navigate class="font-display text-[17px] text-navy transition hover:text-green">Toutes les catégories</a>
                    <a href="{{ route('v2.programmes') }}" wire:navigate class="text-sm font-bold text-green transition hover:text-green-d">Tous les programmes →</a>
                </div>
            </div>
        </div>

        {{-- ---------- Programmes (index par catégorie, colonnes) ---------- --}}
        <div x-show="mega === 'prog'" style="display: none;"
             x-on:mouseenter="mega = 'prog'"
             class="mega-panel absolute inset-x-0 top-full border-t border-line bg-white shadow-xl"
             role="region" aria-label="Programmes">
            <div class="mx-auto max-w-[1200px] px-6 py-8">
                <div class="grid grid-cols-2 gap-x-8 gap-y-7 lg:grid-cols-4">
                    @foreach ($megaCategories as $cat)
                        @php
                            $progs = $cat->programmesOrderByHeightAndActive;
                            $shown = $progs->take(8);
                            $rest  = $progs->count() - $shown->count();
                        @endphp
                        <div>
                            <a href="{{ $cat->canonicalUrl() }}" wire:navigate
                               class="mb-2.5 flex items-center gap-2 font-display text-[18px] text-navy transition hover:text-green">
                                <span class="h-2 w-2 shrink-0 rounded-full bg-green-l"></span>{{ $cat->name }}
                            </a>
                            <ul class="space-y-1.5">
                                @foreach ($shown as $prog)
                                    <li>
                                        <a href="{{ $prog->canonicalUrl() }}" wire:navigate
                                           class="block truncate text-[15px] text-muted transition hover:text-green">{{ $prog->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($rest > 0)
                                <a href="{{ $cat->canonicalUrl() }}" wire:navigate
                                   class="mt-2 inline-block text-sm font-bold text-green transition hover:text-green-d">+ {{ $rest }} autre{{ $rest > 1 ? 's' : '' }} →</a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-7 flex items-center justify-between border-t border-line pt-5">
                    <a href="{{ route('v2.programmes') }}" wire:navigate class="font-display text-[17px] text-navy transition hover:text-green">Tous les programmes</a>
                    <a href="{{ route('v2.emissions') }}" wire:navigate class="text-sm font-bold text-green transition hover:text-green-d">Toutes les émissions →</a>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         MENU MOBILE — navigation à tiroirs (drill-down) avec flèche retour.
         mView pilote le « tiroir » affiché ; mDir donne le sens de l'animation.
         ============================================================ --}}
    <nav id="menu-mobile" x-show="open" style="display:none"
         aria-label="Navigation mobile"
         class="overflow-hidden border-t border-line bg-bg md:hidden">

        {{-- Niveau 0 : menu principal --}}
        <div x-show="mView === 'root'" :class="mDir === 'back' ? 'drawer-back' : 'drawer-fwd'" class="px-4 py-2">
            <button type="button" @click="mDir='fwd'; mView='cat'"
                    class="flex w-full items-center justify-between py-3 font-display text-[19px] text-navy">
                Catégories
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-muted"><path d="m9 6 6 6-6 6"/></svg>
            </button>
            <button type="button" @click="mDir='fwd'; mView='prog'"
                    class="flex w-full items-center justify-between border-t border-line py-3 font-display text-[19px] text-navy">
                Programmes
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-muted"><path d="m9 6 6 6-6 6"/></svg>
            </button>
            <a href="{{ route('v2.emissions') }}" wire:navigate class="block border-t border-line py-3 font-display text-[19px] text-navy">Émissions</a>
            <a href="{{ route('v2.themes') }}" wire:navigate class="block border-t border-line py-3 font-display text-[19px] text-navy">Thèmes</a>
            <a href="{{ route('login') }}" class="block border-t border-line py-3 font-display text-[19px] text-green">Mon compte</a>
        </div>

        {{-- Niveau 1 : catégories --}}
        <div x-show="mView === 'cat'" style="display:none" :class="mDir === 'back' ? 'drawer-back' : 'drawer-fwd'" class="px-4 py-2">
            <button type="button" @click="mDir='back'; mView='root'"
                    class="flex w-full items-center gap-2 py-3 font-display text-[17px] text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="m15 6-6 6 6 6"/></svg>
                Retour
            </button>
            @foreach ($megaCategories as $cat)
                <a href="{{ $cat->canonicalUrl() }}" wire:navigate class="block truncate border-t border-line py-3 font-display text-[19px] text-navy">{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('v2.categories') }}" wire:navigate class="mt-1 block border-t border-line py-3 text-[15px] font-bold text-green">Toutes les catégories →</a>
        </div>

        {{-- Niveau 1 : programmes → choix de la catégorie --}}
        <div x-show="mView === 'prog'" style="display:none" :class="mDir === 'back' ? 'drawer-back' : 'drawer-fwd'" class="px-4 py-2">
            <button type="button" @click="mDir='back'; mView='root'"
                    class="flex w-full items-center gap-2 py-3 font-display text-[17px] text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="m15 6-6 6 6 6"/></svg>
                Retour
            </button>
            @foreach ($megaCategories as $cat)
                <button type="button" @click="mDir='fwd'; mCat={{ $cat->id }}; mView='progOf'"
                        class="flex w-full items-center justify-between border-t border-line py-3 text-left font-display text-[19px] text-navy">
                    <span class="truncate">{{ $cat->name }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 shrink-0 text-muted"><path d="m9 6 6 6-6 6"/></svg>
                </button>
            @endforeach
            <a href="{{ route('v2.programmes') }}" wire:navigate class="mt-1 block border-t border-line py-3 text-[15px] font-bold text-green">Tous les programmes →</a>
        </div>

        {{-- Niveau 2 : programmes d'une catégorie --}}
        <div x-show="mView === 'progOf'" style="display:none" :class="mDir === 'back' ? 'drawer-back' : 'drawer-fwd'" class="px-4 py-2">
            <button type="button" @click="mDir='back'; mView='prog'"
                    class="flex w-full items-center gap-2 py-3 font-display text-[17px] text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="m15 6-6 6 6 6"/></svg>
                Retour
            </button>
            @foreach ($megaCategories as $cat)
                <div x-show="mCat === {{ $cat->id }}" style="display:none">
                    <div class="border-t border-line py-3 font-display text-[15px] uppercase tracking-wide text-muted">{{ $cat->name }}</div>
                    @foreach ($cat->programmesOrderByHeightAndActive as $prog)
                        <a href="{{ $prog->canonicalUrl() }}" wire:navigate class="block truncate border-t border-line py-3 font-display text-[18px] text-navy">{{ $prog->name }}</a>
                    @endforeach
                    <a href="{{ $cat->canonicalUrl() }}" wire:navigate class="mt-1 block border-t border-line py-3 text-[15px] font-bold text-green">Voir la catégorie →</a>
                </div>
            @endforeach
        </div>
    </nav>
</header>
