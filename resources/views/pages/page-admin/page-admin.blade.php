@extends('base')

@push('scripts')
    @vite([
        'resources/js/home/home.js',
        'resources/js/detann.js',
    ])
    <link href="//vjs.zencdn.net/8.3.0/video-js.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@videojs/themes@1/dist/forest/index.css" rel="stylesheet">
@endpush

@push('styles')
@endpush

@section('title', $pageAdmin->name )

@push('metadata')


@endpush
@push('body')
    <article class="section-default pt-2">
        <div class="container">
            <div class="row m-2">
                <div class="col-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route("homepage") }}">Accueil</a></li>
                    </ul>
                </div>
                <div class="col-12 col-xl-7 col-xxl-8">
                    {!! $pageAdmin->content !!}
                </div>
            </div>
        </div>
    </article>
@endpush
@push('scripts')
        @vite('resources/js/detann.js')
@endpush
