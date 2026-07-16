@props(['items' => []])

{{-- Fil d'Ariane. items = [['label' => '…', 'url' => '…' (optionnel pour le dernier)], …] --}}
@if (count($items))
    <nav aria-label="Fil d'Ariane" class="mx-auto max-w-[1200px] px-6 pt-6">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted">
            @foreach ($items as $item)
                <li class="flex items-center gap-2">
                    @if (! empty($item['url']) && ! $loop->last)
                        <a href="{{ $item['url'] }}" wire:navigate class="transition hover:text-green">{{ $item['name'] }}</a>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5 text-line"><path d="m9 6 6 6-6 6"/></svg>
                    @else
                        <span aria-current="page" class="font-semibold text-navy">{{ $item['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
