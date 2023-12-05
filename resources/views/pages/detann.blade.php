@extends('base')

@push('scripts')
    @vite([
        'resources/js/home/home.js',
        'resources/js/detann.js',
    ])
    <link href="//vjs.zencdn.net/8.3.0/video-js.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@videojs/themes@1/dist/forest/index.css" rel="stylesheet">
@endpush

@push('styles')
@endpush

@push('title', $emision->name)

@push('metadata')
    <meta name="description" content="{{ $emision->programme->name }} {{ $emision->name }} - {{ $emision->description }}" />
    <meta property="og:title" content="Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}" />
    <meta property="og:image" content="{{ $emision->image }}" />
    <meta property="og:description" content="{{ Str::words(strip_tags(str_replace('>', '> ', Str::limit($emision->description, 200))) }}" />
    <meta property="og:site_name" content="RadioBastides" />
    <meta property="og:locale" content="fr_FR" />

@endpush
@push('body')
    <article class="section-default pt-2">
        <div class="container">
            <div class="row m-2">
                <div class="col-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("homepage") }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list-programme', [$programme]) }}">Programme {{ $programme->name }}</a></li>
                    </ul>
                </div>
                <div class="col-12 col-xl-7 col-xxl-8">

                    <div class="img-full">
                        <div class="w-100">
                            <h1 class="detann__title text-center rounded-top {{ $emision->media_type }}">{{ $emision->name }}</h1>
                        </div>

                        @if($emision->media_type === "audio")
                            <img src="{{ $emision->image }}" class="img-full w-100 rounded-bottom" alt="Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}">
                            <span id="audio-detann-player" class="calamansi mt-0 pt-0" data-skin="/player-audio/ayon"
                                  data-file-name="Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}"
                                  data-source="{{ $emision->attachment->first()->url !== null ?
                            $emision->attachment->first()->url :
                            '/storage/public/emission/audio/' . $emision->attachment->first()->path . '/' . $emision->attachment->first()->name }}"
                                  data-album-cover="{{ $emision->image }}"
                            >Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}</span>
                        @elseif($emision->media_type === "video")
                            <video-js
                                id="video-detann-player"
                                controls
                                preload="auto"
                                poster="{{ $emision->image }}"
                                class="video-js vjs-theme-forest w-100 rounded-bottom">
                                <source src="{{ $emision->attachment->first()->url ?? '' }}">
                            </video-js>
                        @else
                            <img src="{{ $emision->image }}" class="img-full w-100 rounded-bottom" alt="Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}">
                        @endif



                    </div>

                    <section class="article__administrable_content mb-3">
                        <p class="card-text">{!! $emision->description !!}</p>
                    </section>
                    {{ $emision->active_at }}

                    <div id="fb-root m-3">
                        <script>(function(d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s); js.id = id;
                                js.src = "https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v10.0";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>

                        <!-- Your share button code -->
                        <div class="fb-share-button"
                             data-href="https://radiobastides.fr{{ $_SERVER['REQUEST_URI'] }}"
                             data-layout="button_count">
                        </div>
                    </div>

                    <x-follow-navigation :emision="$emision" />

                    <div class="commentaire">
                        {{-- view/vendor/comments --}}
                        {{-- 3 files --}}
                        @comments(['model' => $emision])
                    </div>

                </div>
                @include('pages.detann.suggestion')
            </div>
        </div>
    </article>
@endpush
@push('scripts')
        @vite('resources/js/detann.js')
@endpush
