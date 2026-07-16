<div x-data="{ filters: false }">
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Le catalogue" title="Toutes nos émissions" />

            {{-- ══ Moteur de filtrage ══ --}}
            <div class="mt-7 rounded-[20px] border border-line bg-white p-4 shadow-sm sm:p-5">
                {{-- Recherche --}}
                <div class="relative">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Rechercher une émission, un sujet…"
                           aria-label="Rechercher une émission"
                           class="w-full rounded-xl border border-line bg-bg py-3.5 pl-12 pr-4 text-[16px] text-ink outline-none transition focus:border-green-l focus:bg-white">
                </div>

                {{-- Onglets type + tri + bouton filtres --}}
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tabs as $tab)
                            <a href="{{ $tab['url'] }}" wire:navigate
                               @class([
                                   'rounded-full border-[1.6px] px-4 py-1.5 font-display text-[16px] transition',
                                   'border-navy bg-navy text-white' => $tab['key'] === $type,
                                   'border-line bg-white text-navy hover:border-green-l' => $tab['key'] !== $type,
                               ])>{{ $tab['label'] }}</a>
                        @endforeach
                    </div>

                    <div class="ml-auto flex items-center gap-2.5">
                        <label class="flex items-center gap-2 text-sm text-muted">
                            <span class="hidden sm:inline">Trier</span>
                            <select wire:model.live="sort"
                                    class="rounded-xl border border-line bg-white py-2 pl-3 pr-8 font-display text-[15px] text-navy outline-none focus:border-green-l">
                                @foreach ($this->sortOptions() as $value => $sortLabel)
                                    <option value="{{ $value }}">{{ $sortLabel }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="button" @click="filters = !filters"
                                :class="filters ? 'border-green-l text-green' : 'border-line text-navy'"
                                class="inline-flex items-center gap-2 rounded-xl border bg-white px-4 py-2 font-display text-[15px] transition hover:border-green-l">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                            Filtres
                            @if ($themes || $duration)
                                <span class="grid h-5 min-w-5 place-items-center rounded-full bg-green px-1 text-[11px] font-bold text-white">{{ count($themes) + ($duration ? 1 : 0) }}</span>
                            @endif
                        </button>
                    </div>
                </div>

                {{-- Panneau repliable : thèmes + durée --}}
                <div x-show="filters" x-collapse x-cloak class="mt-4 border-t border-line pt-4">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-[1fr_auto]">
                        <div>
                            <p class="mb-2 font-display text-[16px] text-navy">Thèmes</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($tagOptions as $tag)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model.live="themes" value="{{ $tag->id }}" class="peer sr-only">
                                        <span class="inline-block rounded-full border-[1.5px] border-line bg-white px-3 py-1 text-[13px] font-semibold text-navy transition hover:border-green-l peer-checked:border-navy peer-checked:bg-navy peer-checked:text-white">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="md:border-l md:border-line md:pl-5">
                            <p class="mb-2 font-display text-[16px] text-navy">Durée</p>
                            <div class="flex flex-col gap-1.5">
                                @foreach ($durationOptions as $value => $durLabel)
                                    <label class="flex cursor-pointer items-center gap-2 text-[15px] text-ink">
                                        <input type="radio" wire:model.live="duration" value="{{ $value }}" class="h-4 w-4 border-line text-green focus:ring-green-l">
                                        {{ $durLabel }}
                                    </label>
                                @endforeach
                                @if ($duration)
                                    <button type="button" wire:click="$set('duration', null)" class="self-start text-xs text-muted hover:text-navy">Effacer la durée</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ligne d'état : compteur + réinitialiser --}}
            <div class="mt-5 flex items-center justify-between">
                <p class="text-sm text-muted" wire:loading.class="opacity-40" wire:target="q,sort,themes,duration">
                    {{ $emissions->total() }} émission{{ $emissions->total() > 1 ? 's' : '' }}
                </p>
                @if ($hasFilters)
                    <button type="button" wire:click="resetFilters" class="text-sm font-semibold text-green transition hover:text-green-d">↺ Réinitialiser</button>
                @endif
            </div>

            {{-- Résultats --}}
            <div wire:target="q,sort,themes,duration,resetFilters" wire:loading.class="opacity-50">
                @if ($emissions->isNotEmpty())
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($emissions as $emision)
                            <x-tall.emission-card :emision="$emision" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $emissions->onEachSide(1)->links() }}</div>
                @else
                    <div class="mt-5 rounded-2xl border border-line bg-white px-6 py-14 text-center">
                        <p class="font-display text-[20px] text-navy">Aucune émission trouvée</p>
                        <p class="mt-1 text-muted">Essayez d'autres mots-clés ou élargissez vos filtres.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <x-tall.footer />
</div>
