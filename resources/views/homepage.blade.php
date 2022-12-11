@extends('base')

@push('body')
    <section class="section-default pt-3">
        <div class="container" >
{{--            <a href="#" class="btn btn-primary">test</a>--}}
{{--            <a href="#" class="btn btn-secondary">test</a>--}}
{{--            <a href="#" class="btn btn-tertiary">test</a>--}}
{{--            <a href="#" class="btn btn-quadrary">test</a>--}}
{{--            <a href="#" class="btn btn-primary btn--small">test</a>--}}
{{--            <a href="#" class="btn btn-secondary btn--small">test</a>--}}
{{--            <a href="#" class="btn btn-tertiary btn--small">test</a>--}}
{{--            <a href="#" class="btn btn-quadrary btn--small">test</a>--}}

                <x-last-home type="audio"></x-last-home>

                <x-last-home type="text"></x-last-home>

                <x-last-home type="video"></x-last-home>
            <div class="section" ></div>
        </div>
    </section>


@endpush
@push('scripts')
    @vite('resources/js/homepage.js')
@endpush
