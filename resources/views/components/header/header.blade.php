<header>

    <div class="header">
        <div><img class="header_logo-rb" srcset="/imgs/logo.png" alt="logo Radiobastides"/></div>
        <nav>
            <ul>
                @foreach($groupProgrammes as $category)
                    <li>
                        <x-link-dropdown :category="$category"/>
                    </li>
                @endforeach
            </ul>
        </nav>
        <x-header-search></x-header-search>
    </div>
    @if(!empty($websiteNews))
        <div id="header_annonce" class="text-container">
            <span class="target">
                @foreach($websiteNews as $websiteNew)
                # {{ $websiteNew->content }}
                @endforeach
            </span>
            <div class="fader fader-left"></div>
            <div class="fader fader-right"></div>
        </div>
    @endif
</header>
