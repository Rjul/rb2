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

                <div class="row w-100">
                    <div class="col-12 col-md-6 col-xl-4 text-center m-auto p-3">
                        <img style="max-width: 200px" src="/storage/img/blanco-peqe.png" class="responsive-img" alt=""> <br>
                        <span style="color: #45a362">La radio de Médias Citoyens en Villeneuvois.</span><br>
                        <a href="https://mediascitoyens.fr/" target="blank"><img style="max-width: 200px" src="/storage/img/media-citoyens-blanco.png" class="responsive-img" alt=""></a>
                    </div>
                    <div class="col-12 col-md-6 col-xl-4 text-center m-auto p-3">
                        <h6><b style="color: #45a362">Mentions légales</b></h6>
                        <span><a class="modal-trigger" href="#mdlfott01" style="color: white; border-bottom: 2px solid #3b995c;">L'association</a></span> <br>
                        <span><a class="modal-trigger" href="#mdlfott02" style="color: white; border-bottom: 2px solid #3b995c;">La responsabilité légale</a></span> <br>
                        <span><a class="modal-trigger" href="#mdlfott03" style="color: white; border-bottom: 2px solid #3b995c;">Protection des données</a> </span>
                    </div>
                    <div class="offset-md-3 col-12 col-md-6 col-xl-4 text-center m-auto p-3">
                        <h6><b style="color: #45a362">Abonnez-vous</b></h6>
                        <a href="https://www.facebook.com/radiobastides" target="blank"><img style="max-width: 70px" src="/storage/img/fb.png" class="responsive-img" alt=""></a>
                        <a href="https://www.instagram.com/radiobastides/" target="blank"><img style="max-width: 70px" src="/storage/img/ig.png" class="responsive-img" alt=""></a>
                        <h6><b style="color: #45a362">Vous pouvez nous écrire à</b></h6>
                        <b><a style="color:white;" href="mailto:contactez-nous@mediascitoyens.fr">contactez-nous@mediascitoyens.fr</a></b>
                    </div>
                </div>
                <img class="rotate_180" src="/imgs/bastides_onde_footer.svg" alt="">
            </div>
        </footer>

        <!-- Modal -->
        <x-modal></x-modal>
    </body>
</html>
