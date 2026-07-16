<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        {{-- En-tête catégorie --}}
        <section class="mx-auto max-w-[1200px] px-6 pt-6">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.15em] text-green">Catégorie</p>
            <h1 class="font-display leading-[1.05] text-navy text-[clamp(32px,4.6vw,52px)]">{{ $category->name }}</h1>
            <x-tall.rich-text :html="$category->description" class="prose-rb mt-4 max-w-[70ch] text-[17px] leading-relaxed text-muted" />
        </section>

        {{-- Programmes de la catégorie --}}
        <section class="mx-auto max-w-[1200px] px-6 pt-12">
            <x-tall.heading kicker="Les programmes" title="Ce que vous trouverez ici" />
            @if ($programmes->isNotEmpty())
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($programmes as $programme)
                        <x-tall.programme-tile :programme="$programme" />
                    @endforeach
                </div>
            @else
                <p class="mt-6 text-muted">Aucun programme actif dans cette catégorie.</p>
            @endif
        </section>

        {{-- Dernières émissions de la catégorie --}}
        @if ($emissions->isNotEmpty())
            <section class="mx-auto max-w-[1200px] px-6 pt-14">
                <x-tall.heading kicker="À écouter, lire et voir" title="Les dernières émissions" />
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($emissions as $emision)
                        <x-tall.emission-card :emision="$emision" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-tall.footer />
</div>
