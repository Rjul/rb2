<div class="{{ $suggestion ? 'col-12' : 'col-sm-12 col-md-6 col-xl-3' }}">
    <article class=" item-card item-card__small">
        <div class="row">
            <div class="col-6">

            </div>
            <div class="col-6">

            </div>
        </div>
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
    </article>
</div>
