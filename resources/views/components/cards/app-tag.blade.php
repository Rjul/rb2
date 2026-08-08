@if($isLarge)

    <a href="{{ route('v2.theme', $tag->slug) }}" class="btn-tag text-black-50 shadow-sm border" style="border-color: {{ $tag->color }} !important;">
        {{ $tag->getTranslation("name", "fr") }}
    </a>

@else
    <a href="{{ route('v2.theme', $tag->slug) }}" class="btn-tag btn-tag--small text-black-50 shadow-sm border" style="border-color: {{ $tag->color }} !important;">
        {{ $tag->getTranslation("name", "fr") }}
    </a>
@endif
