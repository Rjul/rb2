@if (!$lastHomeAudio->isEmpty())

@if($type == "audio")
    <div class="home_separator-title mb-5">
        <h2 class="home_big-title">Nos dernières émissions à écouter.</h2>
    </div>@elseif($type == "text")
    <div class="home_separator-title mb-5">
        <h2 class="home_big-title">Nos derniers articles à lire.</h2>
    </div>
@elseif($type == "video")
    <div class="home_separator-title mb-5">
        <h2 class="home_big-title">Nos dernières émissions vidéo.</h2>
    </div>@endif
<div class="relative d-flex flex-row mt-3 section-lasthome flex-wrap">
    <div>
        @if($type == "audio")
            <span class="home-card_big-title title__ecouter">Ecouter</span>
        @elseif($type == "text")
            <span class="home-card_big-title title__lire">Lire</span>
        @elseif($type == "video")
            <span class="home-card_big-title title__voir">Voir</span>
        @endif
    </div>
    <div class="row gy-3">
    @foreach($lastHomeAudio as $emision)
        @if ($loop->first)
            <x-big-w-card :$emision ></x-big-w-card>
        @else
            <x-small-card :$emision ></x-small-card>
        @endif
    @endforeach
    </div>
</div>
@endif
