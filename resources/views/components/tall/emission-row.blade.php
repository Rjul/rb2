@props(['emision'])

@php
    $prog    = $emision->programme;
    $img     = $emision->image ?: 'https://picsum.photos/seed/rb-' . $emision->id . '/240/240';
    $isAudio = $emision->media_type === 'audio';
    $badge   = ['audio' => 'Audio', 'text' => 'Article', 'video' => 'Vidéo'][$emision->media_type] ?? null;
    $track   = $isAudio ? [
        'title'    => $emision->name,
        'prog'     => $prog?->name,
        'art'      => $img,
        'src'      => $emision->audioUrl(),
        'duration' => $emision->duration ? (int) round($emision->duration * 60) : null,
    ] : null;
@endphp

<a href="{{ $emision->canonicalUrl() }}" wire:navigate
   class="group flex items-center gap-3.5 rounded-2xl border border-line bg-white p-2.5 transition hover:border-green-l hover:shadow-sm">
    <span class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl">
        <img src="{{ $img }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover"
             onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-{{ $emision->id }}/240/240'">
    </span>
    <span class="min-w-0 flex-1">
        @if ($badge)
            <span class="text-[11px] font-bold uppercase tracking-wider text-green">{{ $badge }}</span>
        @endif
        <span class="block truncate font-display text-[16px] leading-tight text-navy group-hover:text-green">{{ $emision->name }}</span>
        @if ($prog)
            <span class="block truncate text-xs text-muted">{{ $prog->name }}</span>
        @endif
    </span>
    @if ($isAudio)
        <button type="button" aria-label="Écouter" x-on:click.stop.prevent="$dispatch('rb:play', @js($track))"
                class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-green text-white transition hover:bg-green-d">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M8 5v14l11-7z"/></svg>
        </button>
    @endif
</a>
