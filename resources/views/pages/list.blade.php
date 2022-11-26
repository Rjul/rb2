@extends('base')

@push('body')

    <div class="app-container d-flex" >
        <aside class="w-25 bg-black">


        </aside>
        <div class="section col-10 row" >
            @foreach($emisions as $emision)
                <x-small-card :$emision ></x-small-card>
            @endforeach

            <div class="section" >
                {{ $emisions->render() }}
            </div>
        </div>

    </div>


@endpush
