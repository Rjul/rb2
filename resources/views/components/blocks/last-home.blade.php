<div class="relative">
    @if($type == "audio")
        <span class="home-card_big-title">Ecouter</span>
    @elseif($type == "text")
        <span class="home-card_big-title">Lire</span>
    @elseif($type == "video")
        <span class="home-card_big-title">Voir</span>
    @endif
    <div class="row">
    @foreach($lastHomeAudio as $emision)
        @if ($loop->first)
            <x-big-w-card :$emision ></x-big-w-card>
        @else
            <x-small-card :$emision ></x-small-card>
        @endif
    @endforeach
    </div>
</div>
