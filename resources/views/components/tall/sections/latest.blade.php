@props(['emissions'])

@php $items = $emissions->values()->take(5); @endphp

@if ($items->isNotEmpty())
    <section id="latest" class="mx-auto max-w-[1200px] px-6 py-16">
        <x-tall.heading kicker="Le meilleur de la Bastide" title="Nos dernières émissions." />

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-[1.45fr_1fr_1fr]">
            @foreach ($items as $e)
                @if ($loop->first)
                    <div class="sm:col-span-2 lg:col-span-1 lg:row-span-2">
                        <x-tall.emission-card :emision="$e" featured />
                    </div>
                @else
                    <x-tall.emission-card :emision="$e" />
                @endif
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('list-search') }}"
               class="inline-flex rounded-xl bg-green px-7 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                Voir toutes nos émissions
            </a>
        </div>
    </section>
@endif
