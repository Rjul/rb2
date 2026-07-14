@props(['programmes'])

@if ($programmes->isNotEmpty())
    <section class="mx-auto max-w-[1200px] px-6 py-14">
        <x-tall.heading kicker="Nos rendez-vous" title="Parcourir les programmes" />
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($programmes as $programme)
                <x-tall.programme-tile :programme="$programme" />
            @endforeach
        </div>
    </section>
@endif
