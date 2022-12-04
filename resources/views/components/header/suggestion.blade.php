@if($emisions)

    @foreach($emisions as $emision)
        <x-small-card :$emision :suggestion="true"> </x-small-card>
    @endforeach

@endif
