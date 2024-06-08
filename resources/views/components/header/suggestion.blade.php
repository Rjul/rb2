<div id="suggestion--container" class="suggestion--container row">
    <div class="suggestion-container-visible p-3 row overflow-auto">
    @if(!$tags->isEmpty() || !$programmes->isEmpty() || !$groups_programme->isEmpty())
        <div class="suggestion__tag-container col-md-4 col-12">
            @if(!$tags->isEmpty())
                <h2 class="mt-3 mb-2 font-bold">Nos thémes</h2>
                <div class="ms-3">
                    <div class="item-card--big_tag-wrapper">
                        @foreach($tags as $tag)
                        <x-tag :$tag ></x-tag>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(!$groups_programme->isEmpty())

                @foreach($groups_programme as $group_programme)
                    {{ $group_programme->name }}
                @endforeach
            @endif
            @if(!$programmes->isEmpty())
                <h2 class="mt-3 mb-2 font-bold">Nos programmes</h2>
                <div class="ms-3">
                    <div class="item-card--big_tag-wrapper">
                    @foreach($programmes as $programme)
                        <a class="border-bottom fs-2 mb-3 lh-base h-100 list-group-item-action text " href="{{ route('list-programme', [ 'programme' => $programme->slug ]) }}"> {{ $programme->name }} </a>
                    @endforeach
                    </div>
                </div>
            @endif

        </div>
    @endif

    @if(!$emisions->isEmpty())
        <div class="suggestion--emissions-container {{$tags->isEmpty() & $programmes->isEmpty() & $groups_programme->isEmpty() ? 'col-12' : 'col-12 col-md-8'}}">
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

