<div class="col-3">
    {{ $emision->name }}
    {{ $emision->description }}
    {{ $emision->image }}
    <a href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}" >Link</a>
    @foreach($emision->tags as $tag)
        <div style="background-color: {{ $tag->color }}">
            {{ $tag->getTranslation("name", "fr") }}
            {{ $tag->id }}
        </div>
    @endforeach
</div>
