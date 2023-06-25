<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>RadioBastides - @yield('title')</title>

        @stack('metadata')
        {{-- Scripts --}}
        @vite(['resources/js/main.js'])
        @stack('scripts')

        @stack('styles')
    </head>
    <body class="body">
        <x-header ></x-header>
        <main class="cd-main-content">
            @stack('body')
        </main>
        <footer class="bg-tertiary">
            <div class="footer_wrapper">
                <img src="/imgs/bastides_onde_footer.svg" alt="">
                <img class="rotate_180" src="/imgs/bastides_onde_footer.svg" alt="">
            </div>
        </footer>
        <!-- Modal -->
        <x-modal></x-modal>
    </body>
</html>
