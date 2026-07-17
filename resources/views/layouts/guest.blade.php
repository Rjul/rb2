<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#001C41">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? 'Espace membre' }} — Radio Bastides</title>

    {{-- Mêmes polices critiques que le reste du site (évite le FOUT) --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/berlin_sans_fb_regular.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/corbel.woff2') }}">

    @vite(['resources/css/tall.css', 'resources/js/tall.js'])
    @livewireStyles
</head>
<body>
    <a href="#contenu"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-navy focus:px-5 focus:py-3 focus:font-display focus:text-white">
        Aller au contenu
    </a>

    <div x-data>
        {{-- Même en-tête et pied que le reste du site : la page reste « dans » le site --}}
        <x-tall.header />

        <main id="contenu" class="mx-auto w-full max-w-[1200px] px-6 py-14 sm:py-20">
            <div class="mx-auto max-w-md rounded-[26px] border border-line bg-white p-7 shadow-sm sm:p-9">
                {{ $slot }}
            </div>
        </main>

        <x-tall.footer />
    </div>

    {{-- Lecteur audio persistant, comme sur toutes les pages du site --}}
    @persist('player')
        <x-tall.player />
    @endpersist

    @livewireScripts
</body>
</html>
