@php $nav = ['Chroniques', 'Magazines', 'Culture', 'Musical']; @endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-line bg-bg/95 backdrop-blur">
    <div class="mx-auto flex h-[76px] max-w-[1200px] items-center gap-7 px-6">
        <a href="{{ route('homepage') }}" class="flex items-center gap-3 font-display text-[26px] text-navy">
            <span class="flex h-[26px] items-end gap-[3px]" aria-hidden="true">
                <span class="w-1 rounded bg-green" style="height:11px"></span>
                <span class="w-1 rounded bg-green" style="height:20px"></span>
                <span class="w-1 rounded bg-green" style="height:26px"></span>
                <span class="w-1 rounded bg-green" style="height:15px"></span>
                <span class="w-1 rounded bg-green" style="height:22px"></span>
            </span>
            Radio&nbsp;Bastides
        </a>

        <nav class="ml-2 hidden gap-8 md:flex">
            @foreach ($nav as $item)
                <a href="#" class="font-display text-[19px] text-navy transition hover:text-green">{{ $item }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-3">
            <button type="button" aria-label="Rechercher"
                    class="grid h-11 w-11 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
            <a href="{{ route('login') }}" aria-label="Mon compte"
               class="grid h-11 w-11 place-items-center rounded-full border border-line bg-white text-navy shadow-sm transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            </a>
            <button type="button" @click="open = !open" aria-label="Menu"
                    class="grid h-11 w-11 place-items-center rounded-full border border-line bg-white text-navy shadow-sm md:hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>

    {{-- menu mobile --}}
    <nav x-show="open" x-transition x-cloak class="border-t border-line bg-bg px-6 py-3 md:hidden">
        @foreach ($nav as $item)
            <a href="#" class="block py-2 font-display text-[19px] text-navy">{{ $item }}</a>
        @endforeach
    </nav>
</header>
