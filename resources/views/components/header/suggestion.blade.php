<div class="suggestion--container row">
    <div class=" p-3  offset-sm-0 col-sm-12 offset-md-4 col-md-8 offset-xl-7 col-xl-5 row bg-white">
    @if($tags || $programme || $groups_programme)
        <div class="suggestion__tag-container col-4">
            @if($tags)
                Nos thémes
                <div class="mt-3">
                @foreach($tags as $tag)
                <x-tag :$tag ></x-tag>
                @endforeach
                </div>
            @endif

        </div>
    @endif

    @if($emisions)
        <div class="suggestion--emissions-container col-8">
            Nos Emissions
            <div class="mt-3">
            @foreach($emisions as $emision)
                <x-small-card :$emision :suggestion="true"> </x-small-card>
            @endforeach
            </div>
        </div>
    @endif
    </div>
</div>

