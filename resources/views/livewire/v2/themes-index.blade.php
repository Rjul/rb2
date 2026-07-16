<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Explorer par sujet" title="Tous les thèmes" />

            @if ($tags->isNotEmpty())
                <div class="mt-8 flex flex-wrap gap-3">
                    @foreach ($tags as $tag)
                        <a href="{{ route('v2.theme', ['tag' => $tag->slug]) }}" wire:navigate
                           class="group inline-flex items-center gap-2 rounded-full border-[1.6px] border-line bg-white px-5 py-2.5 font-display text-[18px] text-navy transition hover:-translate-y-0.5 hover:border-green-l hover:text-green">
                            {{ $tag->name }}
                            <span class="rounded-full bg-green/10 px-2 py-0.5 text-[13px] font-bold text-green">{{ $tag->emisions_count }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-8 text-muted">Aucun thème pour le moment.</p>
            @endif
        </section>
    </main>

    <x-tall.footer />
</div>
