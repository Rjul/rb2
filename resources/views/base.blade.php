<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('content', 'RadioBastides')</title>
        {{-- Scripts --}}
        @vite(['resources/js/main.js'])
        @stack('scripts')
        {{-- facebook script ?--}}
{{--        <script>--}}
{{--            window.fbAsyncInit = function() {--}}
{{--                FB.init({--}}
{{--                    appId      : '{your-app-id}',--}}
{{--                    cookie     : true,--}}
{{--                    xfbml      : true,--}}
{{--                    version    : '{api-version}'--}}
{{--                });--}}

{{--                FB.AppEvents.logPageView();--}}

{{--            };--}}

{{--            (function(d, s, id){--}}
{{--                var js, fjs = d.getElementsByTagName(s)[0];--}}
{{--                if (d.getElementById(id)) {return;}--}}
{{--                js = d.createElement(s); js.id = id;--}}
{{--                js.src = "https://connect.facebook.net/en_US/sdk.js";--}}
{{--                fjs.parentNode.insertBefore(js, fjs);--}}
{{--            }(document, 'script', 'facebook-jssdk'));--}}
{{--        </script>--}}

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
