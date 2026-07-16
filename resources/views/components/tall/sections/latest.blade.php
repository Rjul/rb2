@props(['emissions'])

@php
    $items = $emissions->values()->take(7);
    // Bento gapless : grande maille 2×2, deux mailles larges, le reste en 1×1.
    $spans = [0 => 'col-span-2 row-span-2', 1 => 'col-span-2', 4 => 'col-span-2'];
@endphp

@if ($items->isNotEmpty())
    <section id="latest" class="mx-auto max-w-[1200px] px-6 py-16">
        <x-tall.heading kicker="Le meilleur de la Bastide" title="Nos dernières émissions." />

        <div class="mt-8 grid auto-rows-[210px] grid-cols-2 gap-5 sm:grid-cols-4">
            @foreach ($items as $e)
                <div class="{{ $spans[$loop->index] ?? '' }}">
                    <x-tall.emission-card :emision="$e" :featured="$loop->first" fill />
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('v2.emissions') }}" wire:navigate
               class="inline-flex rounded-xl bg-green px-7 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                Voir toutes nos émissions
            </a>
        </div>
    </section>
@endif
