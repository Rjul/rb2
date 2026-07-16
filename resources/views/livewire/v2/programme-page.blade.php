<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        {{-- Bannière programme --}}
        <section class="mx-auto max-w-[1200px] px-6 pt-6">
            <div class="relative flex min-h-[260px] items-end overflow-hidden rounded-[28px] shadow-xl sm:min-h-[320px]">
                <img src="{{ $programme->image ?: 'https://picsum.photos/seed/rb-p-' . $programme->id . '/1300/500' }}"
                     alt="" class="absolute inset-0 h-full w-full object-cover"
                     onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-p-{{ $programme->id }}/1300/500'">
                <span class="absolute inset-0" style="background:linear-gradient(0deg,rgba(4,20,40,.92) 8%,rgba(4,20,40,.15) 70%)"></span>
                <div class="relative w-full p-7 text-white sm:p-10">
                    @if ($category)
                        <a href="{{ $category->canonicalUrl() }}" wire:navigate class="text-xs font-bold uppercase tracking-[0.15em] text-green-l transition hover:text-white">{{ $category->name }}</a>
                    @endif
                    <h1 class="mt-1 font-display leading-[1.05] text-[clamp(30px,4.4vw,50px)]">{{ $programme->name }}</h1>
                </div>
            </div>

            <x-tall.rich-text :html="$programme->description" class="prose-rb mt-6 max-w-[70ch] text-[17px] leading-relaxed text-muted" />

            @if ($programme->has_rss)
                <a href="{{ route('api-rss-programme', ['programme' => $programme->id]) }}" target="_blank" rel="noopener"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl border-[1.6px] border-line px-5 py-2.5 font-display text-[16px] text-navy transition hover:border-green-l hover:text-green">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M4 11a9 9 0 0 1 9 9h2.5A11.5 11.5 0 0 0 4 8.5V11Zm0 5a3 3 0 0 1 3 3h2.5A5.5 5.5 0 0 0 4 13.5V16Zm1.5 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>
                    S'abonner au podcast (RSS)
                </a>
            @endif
        </section>

        {{-- Émissions du programme --}}
        <section class="mx-auto max-w-[1200px] px-6 pt-12">
            <x-tall.heading kicker="Le programme" title="Toutes les émissions" />

            {{-- Moteur scopé au programme : recherche + type + tri --}}
            <div class="mt-6 flex flex-col gap-3 rounded-[18px] border border-line bg-white p-4 shadow-sm md:flex-row md:items-center">
                <div class="relative md:max-w-xs md:flex-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Rechercher dans ce programme…"
                           aria-label="Rechercher dans ce programme"
                           class="w-full rounded-xl border border-line bg-bg py-2.5 pl-10 pr-3 text-[15px] text-ink outline-none transition focus:border-green-l focus:bg-white">
                </div>

                <div class="flex flex-wrap gap-2 md:ml-1">
                    @foreach ($typeTabs as $tab)
                        <button type="button" wire:click="$set('ptype', @js($tab['key']))"
                                @class([
                                    'rounded-full border-[1.6px] px-3.5 py-1.5 font-display text-[15px] transition',
                                    'border-navy bg-navy text-white' => $tab['key'] === $ptype,
                                    'border-line bg-white text-navy hover:border-green-l' => $tab['key'] !== $ptype,
                                ])>{{ $tab['label'] }}</button>
                    @endforeach
                </div>

                <label class="flex items-center gap-2 text-sm text-muted md:ml-auto">
                    <span class="hidden sm:inline">Trier</span>
                    <select wire:model.live="sort" class="rounded-xl border border-line bg-white py-2 pl-3 pr-8 font-display text-[15px] text-navy outline-none focus:border-green-l">
                        @foreach ($this->sortOptions() as $value => $sortLabel)
                            <option value="{{ $value }}">{{ $sortLabel }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted" wire:loading.class="opacity-40" wire:target="q,sort,ptype">
                    {{ $emissions->total() }} émission{{ $emissions->total() > 1 ? 's' : '' }}
                </p>
                @if ($hasFilters)
                    <button type="button" wire:click="resetFilters" class="text-sm font-semibold text-green transition hover:text-green-d">↺ Réinitialiser</button>
                @endif
            </div>

            <div wire:target="q,sort,ptype,resetFilters" wire:loading.class="opacity-50">
                @if ($emissions->isNotEmpty())
                    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($emissions as $emision)
                            <x-tall.emission-card :emision="$emision" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $emissions->onEachSide(1)->links() }}</div>
                @else
                    <p class="mt-6 text-muted">{{ $hasFilters ? 'Aucune émission ne correspond à ces filtres.' : 'Aucune émission publiée pour le moment.' }}</p>
                @endif
            </div>
        </section>

        {{-- Autres programmes de la catégorie --}}
        @if ($siblings->isNotEmpty())
            <section class="mx-auto max-w-[1200px] px-6 pt-14">
                <x-tall.heading kicker="À découvrir aussi" title="Dans la même catégorie" />
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($siblings as $sibling)
                        <x-tall.programme-tile :programme="$sibling" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-tall.footer />
</div>
