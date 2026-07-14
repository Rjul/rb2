@props(['emision', 'featured' => false, 'fill' => false])

@php
    $prog     = $emision->programme;
    $url      = $prog ? route('view-emision', ['programme' => $prog, 'emision' => $emision]) : '#';
    $img      = $emision->image ?: 'https://picsum.photos/seed/rb-' . $emision->id . '/700/850';
    $isAudio  = $emision->media_type === 'audio';
    $isVideo  = $emision->media_type === 'video';
    $badge    = ['audio' => 'Audio', 'text' => 'Article', 'video' => 'Vidéo'][$emision->media_type] ?? null;
    $duration = $emision->duration ? (int) round($emision->duration) . '′' : null;
    $size     = $fill ? 'h-full' : ($featured ? 'min-h-[480px]' : 'aspect-[4/4.4]');

    // Seules les émissions audio alimentent le lecteur persistant.
    $track = $isAudio ? [
        'title'    => $emision->name,
        'prog'     => $prog?->name,
        'art'      => $img,
        'src'      => $emision->audioUrl(),
        'duration' => $emision->duration ? (int) round($emision->duration * 60) : null,
    ] : null;

    // Style commun des pastilles d'action (factorisé pour éviter la duplication).
    $btn = 'grid h-[42px] w-[42px] place-items-center rounded-full border border-white/40 bg-white/10 text-white backdrop-blur transition';
@endphp

<a href="{{ $url }}"
   class="group relative flex items-end overflow-hidden rounded-card shadow-sm transition duration-200 hover:-translate-y-1.5 hover:shadow-xl {{ $size }}">
    <img src="{{ $img }}" alt="" loading="lazy"
         onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-{{ $emision->id }}/700/850'"
         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">

    @if ($badge)
        <span class="absolute left-3.5 top-3.5 z-10 inline-flex items-center gap-1.5 rounded-full bg-navy-3/60 px-3 py-1 text-xs font-bold text-white backdrop-blur">
            <span class="h-1.5 w-1.5 rounded-full bg-green-l"></span>{{ $badge }}
        </span>
    @endif

    <span class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-navy-3/90"></span>

    <div class="relative w-full p-5 text-white">
        @if ($prog)
            <span class="text-xs font-bold uppercase tracking-wider text-green-l">{{ $prog->name }}</span>
        @endif
        <h3 class="mt-1 font-display leading-tight {{ $featured ? 'max-w-[17ch] text-[31px]' : 'text-[22px]' }}">{{ $emision->name }}</h3>

        @if ($featured && $emision->description)
            <p class="mt-2 line-clamp-2 max-w-[46ch] text-[15px] text-slate-200">{{ \Illuminate\Support\Str::limit(strip_tags($emision->description), 150) }}</p>
        @endif

        <div class="mt-3 flex items-center justify-between">
            <span class="font-display text-[24px] tabular-nums text-green-l">{{ $duration ?? '—' }}</span>

            <span class="flex gap-2">
                @if ($isAudio)
                    {{-- lecteur persistant : file d'attente + lecture --}}
                    <button type="button" aria-label="Ajouter à la file d'attente"
                            x-on:click.stop.prevent="$dispatch('rb:queue', @js($track))"
                            class="{{ $btn }} hover:border-green-l hover:bg-green-l hover:text-navy-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-[17px] w-[17px]"><path d="M4 6h11M4 12h11M4 18h7"/><path d="M18 13v6M15 16h6"/></svg>
                    </button>
                    <button type="button" aria-label="Écouter {{ $emision->name }}"
                            x-on:click.stop.prevent="$dispatch('rb:play', @js($track))"
                            class="{{ $btn }} hover:border-green-l hover:bg-green-l hover:text-navy-3">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-[17px] w-[17px]"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                @else
                    {{-- article / vidéo : le clic sur la carte ouvre la page dédiée --}}
                    <span aria-hidden="true" class="{{ $btn }} group-hover:border-green-l group-hover:bg-green-l group-hover:text-navy-3">
                        @if ($isVideo)
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-[17px] w-[17px]"><path d="M8 5v14l11-7z"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-[18px] w-[18px]"><path d="M12 6.5c-1.6-1-4.2-1.6-6.5-1.6V17c2.3 0 4.9.6 6.5 1.6m0-12.1c1.6-1 4.2-1.6 6.5-1.6V17c-2.3 0-4.9.6-6.5 1.6m0-12.1V18.6"/></svg>
                        @endif
                    </span>
                @endif
            </span>
        </div>
    </div>
</a>
