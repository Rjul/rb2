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
                        <li class="breadcrumb-item"><a href="">Homepage</a></li>
                        <li class="breadcrumb-item"><a href="">Programme</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-6 col-xl-8 col-xxl-9">

                    <div class="img-full">
                        <div class="w-100" style="background: #0D2520 0% 0% no-repeat padding-box;">
                            <h2 class="card-title text-center">{{ $emision->name }}</h2>
                        </div>

                        <img src="{{ $emision->image }}" class="img-full w-100" alt="...">
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
    {{--    @vite('resources/js/homepage.js')--}}
@endpush
