{{-- Lecteur persistant — état géré côté client par Alpine (resources/js/tall/player.js) --}}
<div x-data="player" x-cloak
     class="fixed inset-x-0 bottom-0 z-50 border-t border-white/10 bg-navy-3 text-white">
    <audio x-ref="audio" preload="none"></audio>

    {{-- panneau file d'attente --}}
    <div x-show="open" x-cloak x-transition
         class="absolute bottom-full right-3 mb-3 w-[360px] max-w-[92vw] overflow-hidden rounded-2xl border border-white/10 bg-navy-3 shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
            <span class="font-display text-[16px]">File d'attente (<span x-text="queue.length"></span>)</span>
            <div class="flex items-center gap-3">
                <button type="button" x-show="queue.length" @click="clearQueue()" class="text-xs text-slate-400 transition hover:text-white">Vider</button>
                <button type="button" @click="open = false" aria-label="Fermer" class="text-slate-400 transition hover:text-white">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>
        <div class="max-h-[300px] overflow-y-auto">
            <p x-show="!queue.length" class="px-4 py-6 text-center text-sm text-slate-400">Aucune émission en attente.</p>
            <template x-for="(t, i) in queue" :key="i">
                <div class="flex items-center gap-3 px-4 py-2.5 transition hover:bg-white/5">
                    <img :src="t.art" alt="" class="h-10 w-10 flex-none rounded-lg object-cover bg-white/10">
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-display text-[15px]" x-text="t.title"></div>
                        <div class="truncate text-[12px] text-green-l" x-text="t.prog"></div>
                    </div>
                    <button type="button" @click="playFromQueue(i)" aria-label="Lire" class="grid h-8 w-8 flex-none place-items-center rounded-full bg-green-l text-navy-3 transition hover:bg-white">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <button type="button" @click="removeFromQueue(i)" aria-label="Retirer" class="grid h-8 w-8 flex-none place-items-center rounded-full text-slate-400 transition hover:text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <div class="mx-auto max-w-[1200px] px-4 sm:px-6">
        <div class="flex h-[64px] items-center gap-3 sm:h-[78px] sm:gap-4">
            {{-- pochette --}}
            <div class="h-[44px] w-[44px] flex-none overflow-hidden rounded-xl bg-white/10 sm:h-[52px] sm:w-[52px]">
                <img x-show="current.art" :src="current.art" alt="" class="h-full w-full object-cover">
            </div>

            {{-- titre / programme --}}
            <div class="min-w-0 flex-1 sm:min-w-[120px] sm:flex-none">
                <div class="truncate font-display text-[15px] leading-tight sm:text-[17px]" x-text="hasTrack ? current.title : 'Aucune lecture'"></div>
                <div class="truncate text-[12.5px] text-green-l" x-text="hasTrack ? current.prog : 'Choisissez une émission'"></div>
            </div>

            {{-- play / pause --}}
            <button type="button" @click="toggle()" :disabled="!hasTrack" aria-label="Lecture / pause"
                    class="grid h-[46px] w-[46px] flex-none place-items-center rounded-full bg-green-l text-navy-3 transition hover:bg-white disabled:opacity-40 sm:h-[50px] sm:w-[50px]">
                <svg x-show="!playing" viewBox="0 0 24 24" fill="currentColor" class="h-[22px] w-[22px]"><path d="M8 5v14l11-7z"/></svg>
                <svg x-show="playing" x-cloak viewBox="0 0 24 24" fill="currentColor" class="h-[22px] w-[22px]"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
            </button>

            {{-- progression desktop (dans la rangée) : glissable, poignée au survol --}}
            <div class="hidden flex-1 items-center gap-3 sm:flex">
                <span class="min-w-[46px] text-[12.5px] tabular-nums text-slate-400" x-text="displayTime"></span>
                <x-tall.player-trackbar thumb="hover" class="h-6" />
                <span class="min-w-[46px] text-right text-[12.5px] tabular-nums text-slate-400" x-text="totalTime"></span>
            </div>

            {{-- file d'attente --}}
            <button type="button" @click="open = !open" title="File d'attente" aria-label="File d'attente"
                    class="relative grid h-[44px] w-[44px] flex-none place-items-center rounded-full border border-white/30 text-slate-300 transition hover:border-green-l hover:text-white"
                    :class="open && 'border-green-l text-white'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-[19px] w-[19px]"><path d="M4 6h11M4 12h11M4 18h7"/><path d="M18 13v6M15 16h6"/></svg>
                <span x-show="queue.length" x-cloak x-text="queue.length"
                      class="absolute -right-1.5 -top-1.5 grid h-[19px] min-w-[19px] place-items-center rounded-full bg-green-l px-1 text-[11px] font-bold text-navy-3"></span>
            </button>
        </div>

        {{-- progression mobile : rangée dédiée (temps + grande barre), façon Apple Podcasts --}}
        <div x-show="hasTrack" class="flex items-center gap-2.5 pb-2.5 sm:hidden">
            <span class="min-w-[40px] text-[12px] tabular-nums text-slate-400" x-text="displayTime"></span>
            <x-tall.player-trackbar thumb="always" class="h-7" />
            <span class="min-w-[40px] text-right text-[12px] tabular-nums text-slate-400" x-text="totalTime"></span>
        </div>
    </div>
</div>
