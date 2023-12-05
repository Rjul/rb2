@extends('base')
@push('scripts')
    @vite(['resources/js/home/home.js'])
@endpush
@push('metadata')
    <meta name="description" content="Bienvenue sur RadioBastides, votre source d'informations et de divertissement culturel à Villeneuve-sur-Lot, Lot-et-Garonne. Restez informés des actualités locales et nationales, découvrez nos émissions culturelles, et connectez-vous avec la communauté bastidoise.">
    <meta name="keywords" content="RadioBastides, actualités locales, actualités nationales, divertissement culturel, Villeneuve-sur-Lot, Lot-et-Garonne, émissions culturelles">
@endpush
@push('title')
    Accueil
@endpush
@push('body')
    <section class="section section-default pt-3">
        <div class="container">
            <div class="home_separator-title">
                <h2 class="home_big-title">Nos dernières émissions.</h2>
            </div>
            <x-home-lastest-emissions></x-home-lastest-emissions>

            <div class="w-100 mt-5 center d-flex justify-content-center">
                <a class="mt-5 btn btn-primary font-berlin  m-auto" href="{{ route('list-search') }}">Voir toute nos émissions</a>
            </div>

{{--                <x-last-home type="audio"></x-last-home>--}}

{{--                <x-last-home type="text"></x-last-home>--}}

{{--                <x-last-home type="video"></x-last-home>--}}

        </div>
    </section>
    <section class="section" >
        <x-home-programme></x-home-programme>
    </section>
    <section class="section" >
        <x-home-tags></x-home-tags>
    </section>
@endpush

