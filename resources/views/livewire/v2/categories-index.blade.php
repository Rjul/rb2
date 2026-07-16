<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Explorer" title="Les catégories de la radio" />
            <p class="mt-3 max-w-[60ch] text-[17px] text-muted">
                Parcourez nos univers éditoriaux et plongez dans les programmes qui les composent.
            </p>

            @if ($categories->isNotEmpty())
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <x-tall.category-card :category="$category" />
                    @endforeach
                </div>
            @else
                <p class="mt-8 text-muted">Aucune catégorie pour le moment.</p>
            @endif
        </section>
    </main>

    <x-tall.footer />
</div>
