@if (!$emissions->isEmpty())
    <div class="d-flex flex-row mt-3 flex-wrap">
        <div class="row gy-3">
            @foreach($emissions as $emision)
                @if ($loop->first)
                    <x-big-w-card :$emision ></x-big-w-card>
                @else
                    <x-small-card :$emision ></x-small-card>
                @endif
            @endforeach
        </div>
    </div>
@endif
