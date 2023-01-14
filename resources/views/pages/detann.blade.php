@extends('base')
@push('scripts')
    @vite(['resources/js/home/home.js'])
@endpush
@push('body')
    <section class="section-default pt-2">
        <div class="container-fluid" >
            <div class="row m-5">
                <div class="col-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("homepage") }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list-programme', [$programme]) }}">Programme {{ $programme->name }}</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-6 col-xl-8 col-xxl-9">

                    <div class="img-full">
                        <div class="w-100" style="background: #0D2520 0% 0% no-repeat padding-box;">
                            <h2 class="card-title text-center">{{ $emision->name }}</h2>
                        </div>

                        <img src="{{ $emision->image }}" class="img-full w-100" alt="...">
                    </div>

                    <div class="detann__down_image" >
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
                    <p class="card-text">{{ $emision->description }}</p>

                    <div class="commentaire">
                        {{-- view/vendor/comments --}}
                        {{-- 3 files --}}
                        @comments(['model' => $emision])
                    </div>

                </div>
                <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                    {{-- a side sticky --}}
                    <div class="sticky-rigth mt-3">
                        @foreach($suggestionEmisions as $sug)
                            <div class="m-3">
                                <x-small-card :emision="$sug" :suggestion="true"/>
                            </div>
                        @endforeach
                    </div>
                </div>
    </section>
@endpush
@push('scripts')
        @vite('resources/js/detann.js')
@endpush
