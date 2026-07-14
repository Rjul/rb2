<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Radio Bastides' }}</title>
    @vite(['resources/css/tall.css', 'resources/js/tall.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}

    {{-- Lecteur audio persistant : @persist garde le nœud (et la lecture) vivant à travers wire:navigate --}}
    @persist('player')
        <x-tall.player />
    @endpersist

    @livewireScripts
</body>
</html>
