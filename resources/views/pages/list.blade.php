@extends('base')

@push('body')

    <div class="app-container d-flex" >
        <aside class="w-25 bg-black">


        </aside>
        <div class="section col-10 row">
            <section class="m-3">
                @if ($programme)
                    <h2>Decouvrer notre programme <strong>{{  $programme->name }}</strong></h2>
                <span>{{  $programme->description }}</span>
                @elseif($tag)
                    <h2>Decouvrer notre théme <strong>{{ $tag->getTranslation("name", "fr") }}</strong></h2>
                <span>{{ $tag->description }}</span>
                @endif
            </section>
            @foreach($emisions as $emision)
                <x-small-card :$emision ></x-small-card>
            @endforeach

            <div class="section text-center">
                <div>
                {{ $emisions->render() }}
                </div>
            </div>
        </div>

    </div>


@endpush
