<div x-data>
    <x-tall.header />
    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-16">
        <article class="mx-auto max-w-[1200px] px-6 pt-8">
            <x-tall.heading as="h1" kicker="Radio Bastides" :title="$page->name" />

            {{-- Contenu BO assaini (rich-text) ; .prose-rb applique la charte au HTML libre. --}}
            <x-tall.rich-text :html="$page->content" class="prose-rb mt-8 max-w-[70ch] text-[17.5px] leading-relaxed text-ink" />
        </article>
    </main>

    <x-tall.footer />
</div>
