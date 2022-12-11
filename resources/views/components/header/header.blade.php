<header class="header fixed-top">

{{--    <div class="header">--}}
{{--        <div><img class="header_logo-rb" srcset="/imgs/logo.png" alt="logo Radiobastides"/></div>--}}
{{--        <nav>--}}
{{--            <ul>--}}
{{--                @foreach($groupProgrammes as $category)--}}
{{--                    <li>--}}
{{--                        <x-link-dropdown :category="$category"/>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </nav>--}}
{{--        <x-header-search></x-header-search>--}}
{{--    </div>--}}
{{--    @if(!empty($websiteNews))--}}
{{--        <div id="header_annonce" class="text-container">--}}
{{--            <span class="target">--}}
{{--                @foreach($websiteNews as $websiteNew)--}}
{{--                # {{ $websiteNew->content }}--}}
{{--                @endforeach--}}
{{--            </span>--}}
{{--            <div class="fader fader-left"></div>--}}
{{--            <div class="fader fader-right"></div>--}}
{{--        </div>--}}
{{--    @endif--}}

    <nav class="navbar navbar-expand-lg bg-white px-lg-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('homepage') }}">
                <h1 class="mb-0">
                    <img class="header_logo-rb" width="164" srcset="/imgs/logo.png" alt="Radiobastides @todo seo"/>
                </h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav d-flex justify-content-between w-100">
                    @foreach ($groupProgrammes as $category)
                        <li class="nav-item">
                            <x-link-dropdown :category="$category"/>
                        </li>
                    @endforeach
                </ul>
                <x-header-search></x-header-search>
            </div>
            <div id="place-suggestion" class="place-suggestion position-absolute">

            </div>
        </div>
    </nav>
</header>
