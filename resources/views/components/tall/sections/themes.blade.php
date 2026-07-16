@props(['tags'])

@if ($tags->isNotEmpty())
    <section class="mx-auto max-w-[1200px] px-6 py-14" x-data="{ tab: {{ $tags->first()->id }} }">
        <x-tall.heading kicker="Explorer par sujet" title="Les thèmes que nous avons sélectionnés" />

        <div class="my-7 -mx-6 flex gap-2.5 overflow-x-auto px-6 pb-1 sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0">
            @foreach ($tags as $tag)
                <button type="button" x-on:click="tab = {{ $tag->id }}" :aria-pressed="tab === {{ $tag->id }}"
                        class="shrink-0 whitespace-nowrap rounded-full border-[1.6px] px-5 py-2.5 font-display text-[18px] transition"
                        :class="tab === {{ $tag->id }} ? 'border-navy bg-navy text-white' : 'border-line bg-white text-navy hover:border-green-l'">
                    {{ $tag->name }}
                </button>
            @endforeach
        </div>

        @foreach ($tags as $tag)
            <div x-show="tab === {{ $tag->id }}" x-cloak class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($tag->publishedEmisions(6) as $emision)
                    <x-tall.emission-card :emision="$emision" />
                @empty
                    <p class="text-muted">Aucune émission pour ce thème pour le moment.</p>
                @endforelse
            </div>
        @endforeach
    </section>
@endif
