<div class="">
    @if($type == "audio")
    ECOUTER
    @elseif($type == "text")
    LIRE
    @elseif($type == "video")
    VOIR
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
