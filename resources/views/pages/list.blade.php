@extends('base')

@push('body')

    <div class="app-container d-flex" >
        <x-search-engine></x-search-engine>
        <div class="section row gy-3 w-75">
            <section class="m-3">
                @if ($programme)
                    <h2>Découvrez notre programme <strong>{{  $programme->name }}</strong></h2>
                <span>{{  $programme->description }}</span>
                @elseif($query)
                    <h2>Nos articles et émissions correspondant à votre recherche <strong>{{ $query }}</strong></h2>
                @elseif($tag)
                    <h2>Découvrez notre théme <strong>{{ $tag->getTranslation("name", "fr") }}</strong></h2>
                <span>{{ $tag->description }}</span>
                @endif
            </section>
            @foreach($emisions as $emision)
{{--                <x-small-card :$emision ></x-small-card>--}}
                <x-big-w-card :$emision></x-big-w-card>
            @endforeach

            <div class="section text-center">
                <div>
                {{ $emisions->render() }}
                </div>
            </div>
        </div>

    </div>


@endpush
