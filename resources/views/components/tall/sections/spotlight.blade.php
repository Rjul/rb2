@props(['emision'])

@php
    $img   = $emision->image ?: 'https://picsum.photos/seed/rb-spot/1300/600';
    $prog  = $emision->programme;
    $track = ['title' => $emision->name, 'prog' => $prog?->name, 'art' => $img];
@endphp

<section class="mx-auto max-w-[1200px] px-6 py-4">
    <div class="relative flex min-h-[380px] items-center overflow-hidden rounded-[30px] shadow-xl">
        <img src="{{ $img }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0" style="background:linear-gradient(100deg,rgba(4,20,40,.93) 34%,rgba(4,20,40,.25))"></div>
        <div class="relative max-w-[580px] p-12 text-white">
            <p class="mb-2.5 text-xs font-bold uppercase tracking-[0.15em] text-green-l">Le rendez-vous de la semaine</p>
            <h3 class="font-display text-[clamp(28px,3.6vw,42px)] leading-tight">{{ $emision->name }}</h3>
            @if ($emision->description)
                <p class="mt-3.5 text-slate-200">{{ \Illuminate\Support\Str::limit(strip_tags($emision->description), 140) }}</p>
            @endif
            <button type="button" x-on:click="$dispatch('rb:play', @js($track))"
                    class="mt-5 inline-flex items-center gap-2.5 rounded-xl bg-green px-6 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                <svg viewBox="0 0 24 24" fill="currentColor" class="h-[1.1em] w-[1.1em]"><path d="M8 5v14l11-7z"/></svg>
                Écouter l'émission
            </button>
        </div>
    </div>
</section>
