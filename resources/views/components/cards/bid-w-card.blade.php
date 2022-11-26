<div class="col-6">
    {{ $emision->name }}
    {{ $emision->description }}
    {{ $emision->image }}
    @foreach($emision->tags as $tag)
        <div style="background-color: {{ $tag->color }}">
            {{ $tag->getTranslation("name", "fr") }}
        </div>
    @endforeach
</div>
