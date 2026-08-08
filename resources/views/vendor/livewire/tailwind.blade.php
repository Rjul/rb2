{{--
    Pagination front v2 : vrais liens <a href="?page=N"> (crawlables pour le SEO)
    + wire:navigate (navigation SPA qui préserve le lecteur audio persistant).
    Remplace la vue Livewire par défaut (boutons wire:click non crawlables).

    IMPORTANT : les URL du paginator Livewire sont RELATIVES sans slash de tête
    (Livewire::originalPath() == request()->path(), ex. "emissions/audio"). Comme
    wire:navigate suit réellement l'href, un lien relatif est résolu contre le
    répertoire courant et redouble le dernier segment (/emissions/audio →
    /emissions/emissions/audio ; en prod préfixée /v2/emissions → /v2/v2/emissions).
    On force donc des URL ABSOLUES via url() (préserve ?page=N, passe-through si déjà absolu).
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-center gap-1.5">
        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="grid h-10 min-w-10 place-items-center rounded-xl border border-line px-3 text-muted opacity-40">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
            </span>
        @else
            <a href="{{ url($paginator->previousPageUrl()) }}" wire:navigate rel="prev" aria-label="Page précédente"
               class="grid h-10 min-w-10 place-items-center rounded-xl border border-line px-3 text-navy transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
            </a>
        @endif

        {{-- Numéros --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="grid h-10 min-w-10 place-items-center px-1 text-muted">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid h-10 min-w-10 place-items-center rounded-xl bg-navy px-3 font-display text-[16px] text-white">{{ $page }}</span>
                    @else
                        <a href="{{ url($url) }}" wire:navigate
                           class="grid h-10 min-w-10 place-items-center rounded-xl border border-line px-3 font-display text-[16px] text-navy transition hover:border-green-l hover:text-green">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a href="{{ url($paginator->nextPageUrl()) }}" wire:navigate rel="next" aria-label="Page suivante"
               class="grid h-10 min-w-10 place-items-center rounded-xl border border-line px-3 text-navy transition hover:border-green-l hover:text-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
            </a>
        @else
            <span aria-disabled="true" class="grid h-10 min-w-10 place-items-center rounded-xl border border-line px-3 text-muted opacity-40">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
            </span>
        @endif
    </nav>
@endif
