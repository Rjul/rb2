<div class="{{ $isForTagHome ? 'col-12 col-xl-6 col-xxl-6 mb-3' : 'col-12 col-xl-12 col-xxl-6 mb-3' }}">
    <article class="item-card item-card--big h-100">
        <div class="row h-100">
            <div class="col-10">
                <div class="item-card_left">
                    <div class="row">
                        <div class="col-md-5 col-sm-12">
                            <div class="item-card_img-wrapper">
{{--                                <img data-src="https://via.placeholder.com/243x165.jpeg?text=243x165" alt="{{ $emision->name }}" class="item-card_img item-card_img--big lazyload img-fluid">--}}
                                <img data-src="{{ $emision->image }}" alt="{{ $emision->name }}" class="item-card_img item-card_img--big lazyload img-fluid">

                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12">
                            <div class="item-card_content-wrapper">
                                <h3 class="item-card_content-title--big">{{ $emision->name }}</h3>
                                <p class="item-card_content-txt--big">{{ Str::words(strip_tags(str_replace(['>', '&nbsp;'], ['> ', ' '], Str::limit($emision->description, 200)))) }}</p>
{{--                                <p class="item-card_content-txt--big">Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet Lorem ipsum dolor sit amet, consectetuer adipi elit, sed diam nonummy nibh euismod tincidunt ut laoreet</p>--}}
                                @if ($emision->media_type == 'audio' or $emision->media_type == 'video')
                                    <div class="item-card_content_length--big">
                                        {!! str_contains($emision->duration, '.') || is_null($emision->duration) ? str_replace('.', ':', $emision->duration ) : $emision->duration . ':00'  !!}
                                    </div>
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
                <a href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}" class="item-card--big_cta {{$emision->media_type}}">
                    <div class=" md-sm_rotate_90__innertext">
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
