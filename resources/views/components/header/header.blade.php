<header class="header fixed-top">
    <nav class="navbar navbar-expand-lg bg-white px-lg-4">
        <div class="container-fluid h-100">
            <a class="navbar-brand" href="{{ route('homepage') }}">
                <h1 class="mb-0">
                    <img class="header_logo-rb" width="164" srcset="/imgs/logo.png" alt="Radiobastides logo"/>
                </h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse h-100 " id="navbarNav">
                <ul class="navbar-nav d-flex w-100">
                    @foreach ($groupProgrammes as $category)
                        <x-link-dropdown :category="$category"/>
                    @endforeach
                </ul>
                <x-header-search></x-header-search>
            </div>

        </div>
    </nav>
</header>
@if(!empty($websiteNews))
<div id="header_annonce" class="container text-container">
        <span class="target">
            @foreach($websiteNews as $websiteNew)
                <span># {{ $websiteNew->content }}</span>
            @endforeach
        </span>
    <div class="fader fader-left"></div>
    <div class="fader fader-right"></div>
</div>
@endif
<x-auth-login-modal></x-auth-login-modal>
