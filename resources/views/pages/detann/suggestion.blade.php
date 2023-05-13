<div class="col-12 col-xl-4 col-xxl-3">
    {{-- a side sticky --}}
    <div class="sticky-rigth mt-3">
        @foreach($suggestionEmisions as $sug)
            <div class="m-3">
                <x-small-card :emision="$sug" :suggestion="true"/>
            </div>
        @endforeach
    </div>
</div>
