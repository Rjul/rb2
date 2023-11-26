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
    <section class="section-default pt-3">
        <div class="container" >
                <x-last-home type="audio"></x-last-home>

                <x-last-home type="text"></x-last-home>

                <x-last-home type="video"></x-last-home>
            <div class="section" ></div>
        </div>
    </section>

    <x-home-programme></x-home-programme>

    <x-home-tags></x-home-tags>
@endpush

