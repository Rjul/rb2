<div x-data>
    <x-tall.header />

    <main id="contenu">
        <x-tall.sections.hero :emision="$latest->first()" />
        <x-tall.sections.latest :emissions="$latest" />
        <x-tall.sections.a-la-une :emissions="$une" />
        <x-tall.sections.programmes :programmes="$programmes" />
        <x-tall.sections.themes :tags="$tags" />

        <section class="mx-auto max-w-[1200px] px-6">
            <x-tall.sections.trio-row label="Écouter" title="Nos dernières émissions à écouter." :emissions="$audios" />
            <x-tall.sections.trio-row label="Lire"    title="Nos derniers articles à lire."     :emissions="$texts" />
            <x-tall.sections.trio-row label="Voir"    title="Nos dernières émissions vidéo."     :emissions="$videos" />
        </section>

        @if ($spotlight)
            <x-tall.sections.spotlight :emision="$spotlight" />
        @endif

        <livewire:newsletter />
    </main>

    <x-tall.footer />
</div>
