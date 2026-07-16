<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Nos rendez-vous" title="Tous les programmes" />
            <p class="mt-3 max-w-[60ch] text-[17px] text-muted">
                Chroniques, magazines, plateaux et sessions musicales — l'antenne de la Bastide, catégorie par catégorie.
            </p>
        </section>

        @forelse ($categories as $category)
            <section class="mx-auto max-w-[1200px] px-6 pt-12">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="font-display text-[26px] text-navy">{{ $category->name }}</h2>
                    <a href="{{ $category->canonicalUrl() }}" wire:navigate class="shrink-0 text-sm font-semibold text-green transition hover:text-green-d">Voir la catégorie →</a>
                </div>
                <div class="mt-3 h-1 w-14 rounded bg-green-l"></div>
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($category->programmesOrderByHeightAndActive as $programme)
                        <x-tall.programme-tile :programme="$programme" />
                    @endforeach
                </div>
            </section>
        @empty
            <section class="mx-auto max-w-[1200px] px-6 pt-8">
                <p class="text-muted">Aucun programme actif pour le moment.</p>
            </section>
        @endforelse
    </main>

    <x-tall.footer />
</div>
