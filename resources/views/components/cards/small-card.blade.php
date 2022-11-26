<div class="col-3">
    {{ $emision->title }}
    {{ $emision->description }}
    {{ $emision->image }}
    @foreach($emision->tags as $tag)
        <div style="background-color: {{ $tag->color }}">
            {{ $tag->getTranslation("name", "fr") }}
        </div>
    @endforeach
</div>
