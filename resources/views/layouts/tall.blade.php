<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#001C41">

    <title>{{ $title ?? 'Radio Bastides' }}</title>

    {{-- Préchargement des 2 polices critiques (titres + texte courant) : évite le FOUT --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/berlin_sans_fb_regular.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/corbel.woff2') }}">

    {{-- SEO : passés par les composants full-page via ->layout('layouts.tall', [...]) --}}
    @isset($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endisset

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Cohabitation : tout /v2 reste hors index tant que la bascule n'est pas faite. --}}
    @if (request()->is('v2') || request()->is('v2/*'))
        <meta name="robots" content="noindex, follow">
    @elseif (isset($robots))
        <meta name="robots" content="{{ $robots }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:site_name" content="Radio Bastides">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $ogTitle ?? $title ?? 'Radio Bastides' }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @isset($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endisset
    @isset($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endisset

    @isset($jsonLd)
        {{-- JSON_HEX_TAG échappe < > : impossible de fermer le <script> depuis un nom éditable (anti-XSS). --}}
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>
    @endisset

    @vite(['resources/css/tall.css', 'resources/js/tall.js'])
    @livewireStyles

    {{-- Consentement cookies + Google Analytics (même bandeau que le legacy) --}}
    @include('layouts.tarte-au-citron')
</head>
<body>
    {{-- Lien d'évitement (a11y) : premier élément focusable, visible uniquement au focus --}}
    <a href="#contenu"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-navy focus:px-5 focus:py-3 focus:font-display focus:text-white">
        Aller au contenu
    </a>

    {{ $slot }}

    {{-- Lecteur audio persistant : @persist garde le nœud (et la lecture) vivant à travers wire:navigate --}}
    @persist('player')
        <x-tall.player />
    @endpersist

    @livewireScripts
</body>
</html>
