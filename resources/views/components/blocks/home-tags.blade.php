<div class="container">{{-- je voudrais un section avec des tabs clickables fait un bootstrap 5 please --}}

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        @foreach($tags as $tag)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tag-home-{{ $tag->id }}" data-bs-toggle="pill" data-bs-target="#pills-home-{{ $tag->id }}"
                    type="button" role="tab" aria-controls="pills-home-{{ $tag->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $tag->name }}</button>
        </li>
        @endforeach

    </ul>
    <div class="tab-content" id="pills-tabContent">
    @foreach($tags as $tag)
        <div class="tab-pane fade {{ $loop->first ? ' show active' : '' }}" id="pills-home-{{ $tag->id }}" role="tabpanel" aria-labelledby="tag-home-{{ $tag->id }}">
            <div class="row">
                @foreach($tag->emisionsLimites(6) as $emision)
                    <x-big-w-card :emision="$emision" />

                @endforeach
            </div>
        </div>
    @endforeach



    </div>
</div>
