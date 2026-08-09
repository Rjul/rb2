<div x-data>
    <x-tall.header />

    {{-- Bandeau préversion (utilisateur BO sur une émission non publiée) --}}
    @if ($isPreview)
        <div class="border-b border-amber-300 bg-amber-50">
            <div class="mx-auto flex max-w-[1200px] flex-wrap items-center gap-x-4 gap-y-1 px-6 py-2.5 text-sm text-amber-900">
                <span class="inline-flex items-center gap-2 font-bold uppercase tracking-wide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M2.06 12a10.5 10.5 0 0 1 19.88 0 10.5 10.5 0 0 1-19.88 0Z"/><circle cx="12" cy="12" r="3"/></svg>
                    Prévisualisation
                </span>
                <span>{{ $previewReason }} — invisible pour les visiteurs.</span>
                @if ($editUrl)
                    <a href="{{ $editUrl }}" class="font-semibold underline underline-offset-2 transition hover:text-amber-700">Modifier dans l'admin →</a>
                @endif
            </div>
        </div>
    @endif

    <x-tall.breadcrumb :items="$crumbs" />

    <main id="contenu" class="pb-20">
        <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-12 px-6 pt-6 lg:grid-cols-[minmax(0,1fr)_330px]">
            {{-- ══ Colonne principale ══ --}}
            <article>
                {{-- En-tête --}}
                <header>
                    @if ($prog)
                        <a href="{{ $prog->canonicalUrl() }}" wire:navigate class="text-xs font-bold uppercase tracking-[0.15em] text-green transition hover:text-green-d">{{ $prog->name }}</a>
                    @endif
                    <h1 class="mt-2 font-display leading-[1.05] text-navy text-[clamp(30px,4.4vw,50px)]">{{ $e->name }}</h1>
                    @if ($publishedAt)
                        <p class="mt-3 text-sm text-muted">Publié le {{ $publishedAt }}</p>
                    @endif
                </header>

                {{-- Média selon le type --}}
                <div class="mt-6 overflow-hidden rounded-[24px] shadow-lg">
                    @if ($isAudio)
                        <div class="relative">
                            {{-- Image LCP de la fiche : chargement prioritaire --}}
                            <img src="{{ $img }}" alt="{{ $e->name }}" fetchpriority="high" decoding="async" class="aspect-[16/9] w-full object-cover"
                                 onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-{{ $e->id }}/1200/700'">
                            <span class="absolute inset-0 bg-gradient-to-t from-navy-3/85 to-transparent"></span>
                            <div class="absolute inset-x-0 bottom-0 flex flex-wrap items-center gap-3 p-6">
                                <button type="button" x-on:click="$dispatch('rb:play', @js($track))"
                                        class="inline-flex items-center gap-2.5 rounded-xl bg-green px-6 py-3.5 font-display text-[18px] text-white shadow-lg shadow-green/30 transition hover:bg-green-d">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-[1.1em] w-[1.1em]"><path d="M8 5v14l11-7z"/></svg>
                                    Écouter
                                </button>
                                <button type="button" x-on:click="$dispatch('rb:queue', @js($track))"
                                        class="inline-flex items-center gap-2.5 rounded-xl border-[1.5px] border-white/50 bg-white/15 px-5 py-3.5 font-display text-[18px] text-white backdrop-blur transition hover:bg-white/25">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-[1.1em] w-[1.1em]"><path d="M4 6h11M4 12h11M4 18h7M18 13v6M15 16h6"/></svg>
                                    File d'attente
                                </button>
                            </div>
                        </div>
                    @elseif ($isVideo)
                        <div wire:ignore>
                            <video controls preload="none" poster="{{ $img }}" class="aspect-video w-full bg-navy-3">
                                @if ($videoUrl)<source src="{{ $videoUrl }}">@endif
                                Votre navigateur ne peut pas lire cette vidéo.
                            </video>
                        </div>
                        @unless ($videoUrl)
                            <p class="bg-bg px-5 py-3 text-sm text-muted">La vidéo sera bientôt disponible.</p>
                        @endunless
                    @else
                        <img src="{{ $img }}" alt="{{ $e->name }}" fetchpriority="high" decoding="async" class="aspect-[16/9] w-full object-cover"
                             onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-{{ $e->id }}/1200/700'">
                    @endif
                </div>

                {{-- Contenu administrable --}}
                <x-tall.rich-text :html="$e->description" class="prose-rb mt-8 max-w-[70ch] text-[17.5px] leading-relaxed text-ink" />

                {{-- Thèmes --}}
                @if ($e->tags->isNotEmpty())
                    <div class="mt-8 flex flex-wrap gap-2.5">
                        @foreach ($e->tags as $tag)
                            <a href="{{ route('v2.theme', ['tag' => $tag->slug]) }}" wire:navigate
                               class="rounded-full border-[1.5px] border-line px-4 py-1.5 text-sm font-semibold text-navy transition hover:border-green-l hover:text-green">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Partage --}}
                <div class="mt-8">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($e->canonicalUrl()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-xl border-[1.6px] border-line px-5 py-2.5 font-display text-[16px] text-navy transition hover:border-green-l hover:text-green">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M14 9h3l.4-3H14V4.5c0-.9.3-1.5 1.6-1.5H17V.3C16.7.2 15.7.1 14.6.1 12.3.1 11 1.4 11 3.9V6H8v3h3v9h3V9Z"/></svg>
                        Partager sur Facebook
                    </a>
                </div>

                {{-- Navigation précédent / suivant --}}
                @if ($before || $next)
                    <nav class="mt-10 grid grid-cols-1 gap-4 border-t border-line pt-6 sm:grid-cols-2">
                        <div>
                            @if ($before)
                                <a href="{{ $before->canonicalUrl() }}" wire:navigate class="group block rounded-2xl border border-line p-4 transition hover:border-green-l">
                                    <span class="text-xs font-bold uppercase tracking-wider text-muted">← Précédent</span>
                                    <span class="mt-1 block font-display text-[19px] leading-tight text-navy group-hover:text-green">{{ $before->name }}</span>
                                </a>
                            @endif
                        </div>
                        <div>
                            @if ($next)
                                <a href="{{ $next->canonicalUrl() }}" wire:navigate class="group block rounded-2xl border border-line p-4 text-right transition hover:border-green-l">
                                    <span class="text-xs font-bold uppercase tracking-wider text-muted">Suivant →</span>
                                    <span class="mt-1 block font-display text-[19px] leading-tight text-navy group-hover:text-green">{{ $next->name }}</span>
                                </a>
                            @endif
                        </div>
                    </nav>
                @endif

                {{-- Commentaires (composant Livewire dédié) --}}
                <div class="mt-12">
                    <livewire:v2.comment-thread :emission="$e" :key="'comments-' . $e->id" />
                </div>
            </article>

            {{-- ══ Colonne latérale : à écouter aussi ══ --}}
            <aside class="lg:sticky lg:top-[92px] lg:self-start">
                @if ($suggestions->isNotEmpty())
                    <h2 class="font-display text-[22px] text-navy">Dans le même programme</h2>
                    <div class="mt-5 flex flex-col gap-4">
                        @foreach ($suggestions as $sug)
                            <x-tall.emission-row :emision="$sug" />
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>
    </main>

    <x-tall.footer />
</div>
