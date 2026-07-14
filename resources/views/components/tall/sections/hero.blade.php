@props(['emision' => null])

@php
    $img   = $emision?->image ?: 'https://picsum.photos/seed/rb-hero/900/680';
    $prog  = $emision?->programme;
    $track = ['title' => $emision?->name, 'prog' => $prog?->name, 'art' => $img];
@endphp

<section class="mx-auto max-w-[1200px] px-6 pb-8 pt-14">
    <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-[1.02fr_1.1fr]">
        <div>
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.15em] text-green">La radio associative de Villeneuve-sur-Lot</p>
            <h1 class="font-display leading-[1.02] text-navy text-[clamp(38px,5.4vw,60px)]">
                La voix de la <span class="text-green">Bastide</span>, du Lot-et-Garonne au bout des ondes.
            </h1>
            <p class="mt-5 max-w-[46ch] text-[18px] text-muted">
                Émissions, chroniques et musiques d'ici. Piochez dans nos derniers rendez-vous
                et écoutez-les où que vous soyez, quand vous voulez.
            </p>
            <div class="mt-7 flex flex-wrap gap-3.5">
                @if ($emision)
                    <button type="button" x-on:click="$dispatch('rb:play', @js($track))"
                            class="inline-flex items-center gap-2.5 rounded-xl bg-green px-6 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-[1.1em] w-[1.1em]"><path d="M8 5v14l11-7z"/></svg>
                        Écouter la dernière émission
                    </button>
                @endif
                <a href="#latest" class="inline-flex items-center rounded-xl border-[1.6px] border-line px-6 py-3.5 font-display text-[18px] text-navy transition hover:border-green-l hover:text-green">
                    Toutes nos émissions
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="aspect-[4/3] overflow-hidden rounded-[28px] shadow-2xl">
                <img src="{{ $img }}" alt=""
                     onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-hero/900/680'"
                     class="h-full w-full object-cover">
            </div>
            @if ($emision)
                <div class="absolute -bottom-6 left-0 flex max-w-[330px] items-center gap-3.5 rounded-[20px] bg-white p-4 shadow-xl sm:-left-6">
                    <button type="button" x-on:click="$dispatch('rb:play', @js($track))" aria-label="Écouter"
                            class="grid h-[50px] w-[50px] flex-none place-items-center rounded-full bg-green text-white transition hover:bg-green-d">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <div>
                        <span class="block text-[11.5px] font-bold uppercase tracking-wider text-green">Nouveau · {{ $prog?->name }}</span>
                        <span class="block font-display text-[18px] leading-tight text-navy">{{ $emision->name }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
