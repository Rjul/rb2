@php
    // Navigation principale → hubs v2. wire:navigate partout pour que le
    // lecteur audio persistant survive à la navigation (SPA Livewire).
    $nav = [
        ['label' => 'Catégories', 'url' => route('v2.categories')],
        ['label' => 'Programmes', 'url' => route('v2.programmes')],
        ['label' => 'Émissions',  'url' => route('v2.emissions')],
        ['label' => 'Thèmes',     'url' => route('v2.themes')],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-line bg-bg/95 backdrop-blur">
    <div class="mx-auto flex h-[76px] max-w-[1200px] items-center gap-4 px-6 md:gap-7">
        <a href="{{ route('v2.home') }}" wire:navigate class="flex shrink-0 items-center" aria-label="Radio Bastides — accueil">
            <img src="/imgs/logo.png" alt="Radio Bastides" width="221" height="45" class="h-9 w-auto shrink-0 sm:h-10">
        </a>

        <nav class="ml-2 hidden gap-8 md:flex" aria-label="Navigation principale">
            @foreach ($nav as $item)
                {{-- .hover : Livewire précharge la page au survol → navigation quasi instantanée --}}
                <a href="{{ $item['url'] }}" wire:navigate.hover class="font-display text-[19px] text-navy transition hover:text-green">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">
            <a href="{{ route('v2.search') }}" wire:navigate aria-label="Rechercher"
               class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </a>
            <a href="{{ route('login') }}" aria-label="Mon compte"
               class="hidden h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green sm:grid">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            </a>
            <button type="button" @click="open = !open" aria-label="Menu" :aria-expanded="open" aria-controls="menu-mobile"
                    class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-line bg-white text-navy shadow-sm md:hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>

    {{-- menu mobile --}}
    <nav id="menu-mobile" x-show="open" x-transition x-cloak aria-label="Navigation mobile" class="border-t border-line bg-bg px-6 py-3 md:hidden">
        @foreach ($nav as $item)
            <a href="{{ $item['url'] }}" wire:navigate class="block py-2 font-display text-[19px] text-navy">{{ $item['label'] }}</a>
        @endforeach
        <a href="{{ route('login') }}" class="mt-1 block border-t border-line py-2 pt-3 font-display text-[19px] text-green">Mon compte</a>
    </nav>
</header>
