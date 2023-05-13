@extends('base')
@push('scripts')
    @vite(['resources/js/home/home.js'])
@endpush
@push('body')
    <article class="section-default pt-2">
        <div class="container-fluid" >
            <div class="row m-2">
                <div class="col-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("homepage") }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list-programme', [$programme]) }}">Programme {{ $programme->name }}</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-6 col-xl-8 col-xxl-9">

                    <div class="img-full">
                        <div class="w-100">
                            <h1 class="detann__title text-center rounded-top {{ $emision->media_type }}">{{ $emision->name }}</h1>
                        </div>

                        <img src="{{ $emision->image }}" class="img-full w-100 rounded-bottom" alt="...">
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
