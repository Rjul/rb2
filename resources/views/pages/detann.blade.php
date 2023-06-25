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

@section('title', $emision->name)

@push('metadata')
    <meta property="og:title" content="Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}" />
    <meta property="og:image" content="{{ $emision->image }}" />
    <meta property="og:description" content="{{ $emision->description }}" />
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
                                  data-source="{{ $emision->attachment->first()->url }}"
                                  data-album-cover="{{ $emision->image }}"
                            >Radiobastides - {{ $emision->programme->name }} {{ $emision->name }}</span>
                        @elseif($emision->media_type === "video")
                            <video-js
                                id="video-detann-player"
                                controls
                                preload="auto"
                                poster="{{ $emision->image }}"
                                class="video-js vjs-theme-forest w-100 rounded-bottom">
                                <source src="{{ $emision->attachment->first()->url }}">
                            </video-js>
                        @endif



                    </div>

                    <section class="article__administrable_content mb-3">
                        <p class="card-text">{!! $emision->description !!}</p>
                    </section>

                    <section class="detann__down_image mt-5">
                        <h2 class="m-3 h1">Poursuivre parmi les émissions du programme <u>{{ $emision->programme->name }}</u></h2>
                        <div class="d-flex w-100 flex-row justify-content-around align-items-center">
                            <div class="detann__next-and-preview">
                                <h3 class="detann__next_title h2">{{ $emision->name }}</h3>
                                <svg class="svg__flech svg__flech_reversed" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 1080">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: #000000;
                                            }
                                        </style>
                                    </defs>
                                    <path id="Tracé_11" data-name="Tracé 11" class="cls-1" d="M342.89,770.24l-3.05-460.48,400.31,227.61-397.26,232.87Z"/>
                                </svg>
                            </div>
                            <div class="detann__next-and-preview">
                                <h3 class="detann__preview_title h2">{{ $emision->name }}</h3>
                                <svg class="svg__flech" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 1080">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: #000000;
                                            }
                                        </style>
                                    </defs>
                                    <path id="Tracé_11" data-name="Tracé 11" class="cls-1" d="M342.89,770.24l-3.05-460.48,400.31,227.61-397.26,232.87Z"/>
                                </svg>
                            </div>
                        </div>
                    </section>

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
