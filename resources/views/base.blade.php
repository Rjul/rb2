<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('content', 'RadioBastides')</title>
        {{-- Scripts --}}
        @vite(['resources/js/app.js', 'resources/css/app.scss'])
        @stack('scripts')
        <!-- Styles -->
        @stack('styles')
    </head>
    <body class="body">
        <x-header ></x-header>
        <main class="cd-main-content">
            @stack('body')
        </main>
        <footer class="bg-tertiary">

        </footer>
    </body>
</html>
