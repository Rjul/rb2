@props(['kicker' => null, 'title', 'as' => 'h2'])

<div>
    @if ($kicker)
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.15em] text-green">{{ $kicker }}</p>
    @endif
    <{{ $as }} class="font-display leading-[1.05] text-navy text-[clamp(30px,4.4vw,48px)]">{{ $title }}</{{ $as }}>
    <div class="mt-4 h-1 w-14 rounded bg-green-l"></div>
</div>
