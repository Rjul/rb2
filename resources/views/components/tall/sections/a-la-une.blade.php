@props(['emissions'])

@php
    $items = $emissions->map(fn ($e) => [
        'prog'  => $e->programme?->name,
        'title' => $e->name,
        'desc'  => \Illuminate\Support\Str::limit(strip_tags($e->description), 160),
        'dur'   => $e->duration ? (int) round($e->duration) . '′' : '',
        'img'   => $e->image ?: 'https://picsum.photos/seed/rb-une-' . $e->id . '/1600/900',
        'url'   => $e->programme ? route('view-emision', ['programme' => $e->programme, 'emision' => $e]) : '#',
    ])->values();
@endphp

@if ($items->isNotEmpty())
    <section class="relative my-8 min-h-[600px] overflow-hidden bg-navy"
             x-data="{
                items: @js($items),
                i: 0,
                front: true,
                touched: false,
                get cur() { return this.items[this.i] },
                go(n) {
                    const url = `url('${this.items[n].img}')`;
                    const showing = this.front ? this.$refs.bgA : this.$refs.bgB;
                    const hidden  = this.front ? this.$refs.bgB : this.$refs.bgA;
                    hidden.style.backgroundImage = url;
                    hidden.style.opacity = 1;
                    showing.style.opacity = 0;
                    this.front = !this.front;
                    this.i = n;
                },
                init() {
                    this.$refs.bgA.style.backgroundImage = `url('${this.items[0].img}')`;
                    this.$refs.bgA.style.opacity = 1;
                    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        setInterval(() => { if (!this.touched) this.go((this.i + 1) % this.items.length) }, 7000);
                    }
                }
             }">
        <div x-ref="bgA" class="absolute inset-0 bg-cover bg-center opacity-0 transition-opacity duration-700"></div>
        <div x-ref="bgB" class="absolute inset-0 bg-cover bg-center opacity-0 transition-opacity duration-700"></div>
        <div class="absolute inset-0" style="background:linear-gradient(90deg,rgba(2,16,34,.94) 0%,rgba(2,16,34,.72) 38%,rgba(2,16,34,.15) 75%),linear-gradient(0deg,rgba(2,16,34,.88) 0%,rgba(2,16,34,0) 40%)"></div>

        <div class="relative mx-auto flex min-h-[600px] max-w-[1200px] flex-col justify-between gap-11 px-6 py-16">
            <div>
                <p class="mb-1.5 text-xs font-bold uppercase tracking-[0.15em] text-green-l">À la une cette semaine</p>
                <h2 class="max-w-[17ch] font-display leading-none text-white text-[clamp(34px,4.6vw,58px)]" x-text="cur.title"></h2>
                <p class="mt-4 max-w-[52ch] text-[17px] text-slate-300" x-text="cur.desc"></p>
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <button type="button" x-on:click="$dispatch('rb:play', { title: cur.title, prog: cur.prog, art: cur.img })"
                            class="inline-flex items-center gap-2.5 rounded-xl bg-green px-6 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-[1.1em] w-[1.1em]"><path d="M8 5v14l11-7z"/></svg>
                        Écouter
                    </button>
                    <a :href="cur.url"
                       class="inline-flex items-center rounded-xl border-[1.5px] border-white/40 bg-white/15 px-6 py-3.5 font-display text-[18px] text-white transition hover:bg-white/25">
                        Découvrir l'émission
                    </a>
                    <span class="font-display text-[26px] text-green-l" x-text="cur.dur"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-4">
                <template x-for="(it, idx) in items" :key="idx">
                    <button type="button" x-on:click="touched = true; go(idx)" :aria-pressed="i === idx"
                            class="relative aspect-video overflow-hidden rounded-2xl shadow-lg transition hover:-translate-y-0.5">
                        <img :src="it.img" alt="" class="absolute inset-0 h-full w-full object-cover">
                        <span class="absolute inset-0 transition"
                              :class="i === idx ? 'rounded-2xl ring-2 ring-green-l ring-inset' : 'bg-navy-3/55'"></span>
                        <span class="absolute inset-x-3 bottom-2.5 text-left">
                            <span class="block text-[10.5px] font-bold uppercase tracking-wider text-green-l" x-text="it.prog"></span>
                            <span class="block font-display text-[16px] leading-tight text-white" x-text="it.title"></span>
                        </span>
                    </button>
                </template>
            </div>
        </div>
    </section>
@endif
