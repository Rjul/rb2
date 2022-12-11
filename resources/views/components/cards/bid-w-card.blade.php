<div class="col-lg-12 col-xl-6">
    <article class="item-card item-card--big">
        <div class="row">
            <div class="col-10">
                <div class="item-card_left">
                    <div class="row">
                        <div class="col-lg-5 col-sm-12">
                            <div class="item-card_img-wrapper">
{{--                                <img data-src="https://via.placeholder.com/243x165.jpeg?text=243x165" alt="{{ $emision->name }}" class="item-card_img item-card_img--big lazyload img-fluid">--}}
                                <img data-src="{{ $emision->image }}" alt="{{ $emision->name }}" class="item-card_img item-card_img--big lazyload img-fluid" width="265">

                            </div>
                        </div>
                        <div class="col-lg-7 col-sm-12">
                            <div class="item-card_content-wrapper">
                                <h3 class="item-card_content-title--big">{{ $emision->name }}</h3> {{ $emision->programme->name }}
                                <p class="item-card_content-txt--big">{{ $emision->description }}</p>
                                @if ($emision->media_type == 'audio' or $emision->media_type == 'video')
                                    <div class="item-card_content_length--big">55:55</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="item-card--big_tag-wrapper">
                            @foreach($emision->tags as $tag)
                                <x-tag :$tag ></x-tag>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <a href="#@todo:detaiPrgramme" class="item-card--big_cta {{$emision->media_type}}">
                    <div class="md-sm_rotate_90__innertext">
                    @if ($emision->media_type == 'audio')
                        Ecouter
                    @elseif ($emision->media_type == 'text')
                        Lire
                    @elseif ($emision->media_type == 'video')
                        Voir
                    @endif
                    </div>
                </a>
            </div>
        </div>
    </article>
</div>
{{--{{ dump($emision)  }}--}}
