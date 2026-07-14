{{-- Lecteur persistant — état géré côté client par Alpine (resources/js/tall/player.js) --}}
<div x-data="player" x-cloak
     class="fixed inset-x-0 bottom-0 z-50 border-t border-white/10 bg-navy-3 text-white">
    <audio x-ref="audio" preload="none"></audio>

    <div class="mx-auto flex h-[78px] max-w-[1200px] items-center gap-4 px-6">
        {{-- pochette --}}
        <div class="h-[52px] w-[52px] flex-none overflow-hidden rounded-xl bg-white/10">
            <img x-show="current.art" :src="current.art" alt="" class="h-full w-full object-cover">
        </div>

        {{-- titre / programme --}}
        <div class="min-w-[130px]">
            <div class="font-display text-[17px] leading-tight" x-text="hasTrack ? current.title : 'Aucune lecture'"></div>
            <div class="text-[12.5px] text-green-l" x-text="hasTrack ? current.prog : 'Choisissez une émission'"></div>
        </div>

        {{-- play / pause --}}
        <button type="button" @click="toggle()" :disabled="!hasTrack" aria-label="Lecture / pause"
                class="grid h-[50px] w-[50px] flex-none place-items-center rounded-full bg-green-l text-navy-3 transition hover:bg-white disabled:opacity-40">
            <svg x-show="!playing" viewBox="0 0 24 24" fill="currentColor" class="h-[22px] w-[22px]"><path d="M8 5v14l11-7z"/></svg>
            <svg x-show="playing" x-cloak viewBox="0 0 24 24" fill="currentColor" class="h-[22px] w-[22px]"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
        </button>

        {{-- progression --}}
        <div class="hidden flex-1 items-center gap-3 md:flex">
            <span class="min-w-[46px] text-[12.5px] tabular-nums text-slate-400" x-text="currentTime"></span>
            <div class="relative h-1.5 flex-1 cursor-pointer overflow-hidden rounded-md bg-white/15" @click="seek($event)">
                <div class="absolute inset-y-0 left-0 bg-green-l" :style="`width:${progress}%`"></div>
            </div>
            <span class="min-w-[46px] text-right text-[12.5px] tabular-nums text-slate-400" x-text="totalTime"></span>
        </div>

        {{-- file d'attente --}}
        <button type="button" title="File d'attente" aria-label="File d'attente"
                class="relative grid h-[44px] w-[44px] flex-none place-items-center rounded-full border border-white/30 text-slate-300 transition hover:border-green-l hover:text-white">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-[19px] w-[19px]"><path d="M4 6h11M4 12h11M4 18h7"/><path d="M18 13v6M15 16h6"/></svg>
            <span x-show="queue.length" x-cloak x-text="queue.length"
                  class="absolute -right-1.5 -top-1.5 grid h-[19px] min-w-[19px] place-items-center rounded-full bg-green-l px-1 text-[11px] font-bold text-navy-3"></span>
        </button>
    </div>
</div>
