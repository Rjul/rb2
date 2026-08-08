@props([
    /* 'hover' : poignée visible au survol/drag (desktop) — 'always' : toujours visible (mobile) */
    'thumb' => 'hover',
])

{{--
    Barre de progression du lecteur, glissable (scrub) au doigt et à la souris.
    Vit dans le scope Alpine `player` (scrubStart/Move/End, displayProgress…).
    La zone de toucher fait la hauteur du conteneur (h-*) — bien plus grande
    que le trait visible ; touch-action:none évite que le scroll vole le geste.
--}}
<div {{ $attributes->merge(['class' => 'group relative flex-1 cursor-pointer touch-none select-none']) }}
     data-player-trackbar
     role="slider" tabindex="0" aria-label="Position de lecture"
     :aria-valuenow="Math.round(displayProgress)" aria-valuemin="0" aria-valuemax="100"
     :aria-valuetext="`${displayTime} sur ${totalTime}`"
     @pointerdown="scrubStart($event)" @pointermove="scrubMove($event)"
     @pointerup="scrubEnd()" @pointercancel="scrubEnd()"
     @keydown.arrow-right.prevent="nudge(10)" @keydown.arrow-left.prevent="nudge(-10)">
    {{-- piste --}}
    <div class="pointer-events-none absolute inset-x-0 top-1/2 h-1.5 -translate-y-1/2 rounded-md bg-white/15"></div>
    {{-- portion lue --}}
    <div class="pointer-events-none absolute left-0 top-1/2 h-1.5 -translate-y-1/2 rounded-md bg-green-l"
         :style="`width:${displayProgress}%`"></div>
    {{-- poignée --}}
    <div class="pointer-events-none absolute top-1/2 h-3.5 w-3.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white shadow
                @if ($thumb === 'hover') opacity-0 transition group-hover:opacity-100 @endif"
         @if ($thumb === 'hover') :class="scrubbing && '!opacity-100'" @else x-show="hasTrack" @endif
         :style="`left:${displayProgress}%`"></div>
</div>
