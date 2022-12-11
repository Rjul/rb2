@extends('base')
@push('scripts')
    @vite(['resources/js/home/home.js'])
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
@push('scripts')
    @vite('resources/js/homepage.js')
@endpush
