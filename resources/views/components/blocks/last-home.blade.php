<div class="relative d-flex flex-row mt-5 section-lasthome flex-wrap">
    <div>
        @if($type == "audio")
            <span class="home-card_big-title title__ecouter">Ecouter</span>
        @elseif($type == "text")
            <span class="home-card_big-title title__lire">Lire</span>
        @elseif($type == "video")
            <span class="home-card_big-title title__voir">Voir</span>
        @endif
    </div>
    <div class="row ms-md-3 ms-xl-3 ms-sm-0">
    @foreach($lastHomeAudio as $emision)
        @if ($loop->first)
            <x-big-w-card :$emision ></x-big-w-card>
        @else
            <x-small-card :$emision ></x-small-card>
        @endif
    @endforeach
    </div>
</div>
