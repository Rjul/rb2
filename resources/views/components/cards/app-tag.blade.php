@if($isLarge)

<a href="{{ route('list-tag', $tag->slug) }}" class="btn-tag" style="background-color: {{ $tag->color }}">
    {{ $tag->getTranslation("name", "fr") }}
</a>

@else


@endif
