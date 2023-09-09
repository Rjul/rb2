@extends('base')

@push('body')
<div class="container-fluid">
    <div class="app-container row flex-nowrap">
            <x-search-engine></x-search-engine>
            <div class="section col py-3">
                @if ($programme || $query || $tag)
                <section class="m-3 mt-5">
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
                @endif
                <section class="p-3 mt-2 row">
                @foreach($emisions as $emision)
    {{--                <x-small-card :$emision ></x-small-card>--}}
                    <x-big-w-card :$emision></x-big-w-card>
                @endforeach
                </section>
                <div class="section text-center">
                    <div>
                    {{ $emisions->render() }}
                    </div>
                </div>
            </div>

    </div>
</div>


@endpush
