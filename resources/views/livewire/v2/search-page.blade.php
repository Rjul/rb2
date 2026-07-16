<div x-data="{ facets: false }">
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Recherche" title="Trouver une émission" />

            {{-- Barre de recherche --}}
            <div class="relative mt-6">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" wire:model.live.debounce.400ms="q" placeholder="Mots-clés, titre, sujet…"
                       aria-label="Rechercher une émission"
                       class="w-full rounded-2xl border border-line bg-white py-4 pl-12 pr-4 text-[17px] text-ink outline-none transition focus:border-green-l">
                <div wire:loading.flex wire:target="q" class="absolute right-4 top-1/2 -translate-y-1/2 items-center text-sm text-muted">…</div>
            </div>

            <button type="button" @click="facets = !facets"
                    class="mt-4 inline-flex items-center gap-2 rounded-xl border border-line bg-white px-4 py-2.5 font-display text-[16px] text-navy lg:hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                Filtres
            </button>

            <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-[260px_minmax(0,1fr)]">
                {{-- ══ Facettes ══ --}}
                <aside :class="facets ? 'block' : 'hidden'" class="lg:block">
                    <div class="flex flex-col gap-7 rounded-2xl border border-line bg-white p-5">
                        @if ($hasFilters)
                            <button type="button" wire:click="resetFilters" class="self-start text-sm font-semibold text-green hover:text-green-d">↺ Réinitialiser</button>
                        @endif

                        {{-- Type --}}
                        <fieldset>
                            <legend class="mb-2.5 font-display text-[18px] text-navy">Type</legend>
                            <div class="flex flex-col gap-2">
                                @foreach ($typeOptions as $value => $label)
                                    <label class="flex cursor-pointer items-center gap-2.5 text-[15px] text-ink">
                                        <input type="checkbox" wire:model.live="types" value="{{ $value }}"
                                               class="h-4 w-4 rounded border-line text-green focus:ring-green-l">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Durée --}}
                        <fieldset>
                            <legend class="mb-2.5 font-display text-[18px] text-navy">Durée</legend>
                            <div class="flex flex-col gap-2">
                                @foreach ($durationOptions as $value => $label)
                                    <label class="flex cursor-pointer items-center gap-2.5 text-[15px] text-ink">
                                        <input type="radio" wire:model.live="duration" value="{{ $value }}"
                                               class="h-4 w-4 border-line text-green focus:ring-green-l">
                                        {{ $label }}
                                    </label>
                                @endforeach
                                @if ($duration)
                                    <button type="button" wire:click="$set('duration', null)" class="self-start text-xs text-muted hover:text-navy">Effacer</button>
                                @endif
                            </div>
                        </fieldset>

                        {{-- Thèmes --}}
                        @if ($tagOptions->isNotEmpty())
                            <fieldset>
                                <legend class="mb-2.5 font-display text-[18px] text-navy">Thèmes</legend>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($tagOptions as $tag)
                                        <button type="button" wire:click="toggleValue('tags', {{ $tag->id }})"
                                                @class([
                                                    'rounded-full border px-3 py-1 text-[13px] font-semibold transition',
                                                    'border-navy bg-navy text-white' => in_array($tag->id, $tags),
                                                    'border-line bg-white text-navy hover:border-green-l' => ! in_array($tag->id, $tags),
                                                ])>{{ $tag->name }}</button>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        {{-- Programmes --}}
                        @if ($programmeOptions->isNotEmpty())
                            <fieldset>
                                <legend class="mb-2.5 font-display text-[18px] text-navy">Programmes</legend>
                                <div class="flex max-h-52 flex-col gap-2 overflow-y-auto pr-1">
                                    @foreach ($programmeOptions as $programme)
                                        <label class="flex cursor-pointer items-center gap-2.5 text-[15px] text-ink">
                                            <input type="checkbox" wire:model.live="programmes" value="{{ $programme->id }}"
                                                   class="h-4 w-4 shrink-0 rounded border-line text-green focus:ring-green-l">
                                            <span class="truncate">{{ $programme->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif
                    </div>
                </aside>

                {{-- ══ Résultats ══ --}}
                <div wire:target="q,types,tags,programmes,duration,resetFilters,toggleValue" wire:loading.class="opacity-50">
                    <p class="mb-5 text-sm text-muted">{{ $emissions->total() }} résultat{{ $emissions->total() > 1 ? 's' : '' }}</p>

                    @if ($emissions->isNotEmpty())
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($emissions as $emision)
                                <x-tall.emission-card :emision="$emision" />
                            @endforeach
                        </div>
                        <div class="mt-10">{{ $emissions->onEachSide(1)->links() }}</div>
                    @else
                        <div class="rounded-2xl border border-line bg-white px-6 py-14 text-center">
                            <p class="font-display text-[20px] text-navy">Aucun résultat</p>
                            <p class="mt-1 text-muted">Essayez d'autres mots-clés ou élargissez vos filtres.</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <x-tall.footer />
</div>
