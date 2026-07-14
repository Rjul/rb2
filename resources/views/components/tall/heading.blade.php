@props(['kicker' => null, 'title'])

<div>
    @if ($kicker)
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.15em] text-green">{{ $kicker }}</p>
    @endif
    <h2 class="font-display leading-[1.05] text-navy text-[clamp(30px,4.4vw,48px)]">{{ $title }}</h2>
    <div class="mt-4 h-1 w-14 rounded bg-green-l"></div>
</div>
