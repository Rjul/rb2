<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('content', 'RadioBastides')</title>
    {{-- Scripts --}}
    @stack('scripts')
    @vite(['resources/js/app.js', 'resources/css/app.scss'])
    <!-- Styles -->
    @stack('styles')
</head>
<body class="body">
    <x-header ></x-header>
        @stack('body')
    </body>
</html>
