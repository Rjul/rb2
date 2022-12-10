<div class="{{ $suggestion ? 'col-12' : 'col-12 col-lg-6 col-xl-3' }}">
    <article class="item-card item-card--small h-100">

        <div class="item-card_left item-card--small_left h-100">
            <div class="row">
                <div class="col-6">
                    <div class="item-card_img-wrapper">
                        {{--                                <img data-src="https://via.placeholder.com/243x165.jpeg?text=243x165" alt="{{ $emision->name }}" class="item-card_img item-card_img--big lazyload img-fluid">--}}
                        <img data-src="{{ $emision->image }}" alt="{{ $emision->name }}" class="item-card_img item-card_img--small lazyload img-fluid" width="265">
                    </div>
                    @if ($emision->media_type == 'audio' or $emision->media_type == 'video')
                        <div class="item-card_content_length--small mt-4">55:55</div>
                    @endif

                </div>
                <div class="col-6">
                    <div class="item-card_content-wrapper">
                        <h3 class="item-card_content-title--small">{{ $emision->name }}</h3>
                        <p class="item-card_content-txt--small">{{ $emision->description }}</p>
                    </div>

                </div>

            </div>
            <div class="row mt-auto">
                <div class="col-6 h-100">

                    <div class="item-card--small_tag-wrapper">
                        @foreach($emision->tags as $tag)
                            <div class="btn-tag btn-tag--small" style="background-color: {{ $tag->color }}">
                                {{ $tag->getTranslation("name", "fr") }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-6 h-100">
                    <div class="item-card--small_cta-wrapper">
                        <a href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}" class="item-card--small_cta {{$emision->media_type}}">
                            @if ($emision->media_type == 'audio')
                                Ecouter
                            @elseif ($emision->media_type == 'text')
                                Lire
                            @elseif ($emision->media_type == 'video')
                                Voir
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>
