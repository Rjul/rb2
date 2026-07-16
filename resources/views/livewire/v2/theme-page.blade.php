<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <section class="mx-auto max-w-[1200px] px-6 pt-6">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.15em] text-green">Thème</p>
            <h1 class="font-display leading-[1.05] text-navy text-[clamp(32px,4.6vw,52px)]">#{{ $theme->name }}</h1>
            <p class="mt-3 text-[17px] text-muted">{{ $emissions->total() }} émission{{ $emissions->total() > 1 ? 's' : '' }} sur ce thème.</p>
        </section>

        <section class="mx-auto max-w-[1200px] px-6 pt-8">
            @if ($emissions->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($emissions as $emision)
                        <x-tall.emission-card :emision="$emision" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $emissions->onEachSide(1)->links() }}</div>
            @else
                <p class="text-muted">Aucune émission pour ce thème pour le moment.</p>
            @endif
        </section>
    </main>

    <x-tall.footer />
</div>
