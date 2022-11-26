@extends('base')

@push('body')

    <div class="app-container" >
        <div class="section" >
            @foreach($emisions as $emision)
                @dump($emision->programme_id)
                <x-small-card :$emision ></x-small-card>
            @endforeach
        </div>
        <div class="section" >

        </div>
    </div>


@endpush
