@extends('base')

@push('body')

    <div class="app-container" >
        <div class="section" >
            @foreach($lastHomeAudio as $emision)
                <x-small-card :$emision ></x-small-card>
            @endforeach
        </div>
        <div class="section" >

        </div>
    </div>


@endpush
