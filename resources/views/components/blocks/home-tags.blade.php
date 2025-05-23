<div class="container mb-5 mt-5">
    <h2 class="home_big-title m-3 mb-5 h2">Les thèmes que nous avons sélectionnés</h2>
    <ul class="nav nav-pills m-3 mb-5 border-bottom text-center" id="pills-tab" role="tablist">
        @foreach($tags as $tag)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tag-home-{{ $tag->id }}" data-bs-toggle="pill" data-bs-target="#pills-home-{{ $tag->id }}"
                    type="button" role="tab" aria-controls="pills-home-{{ $tag->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"><h3>{{ $tag->name }}</h3></button>
        </li>
        @endforeach

    </ul>
    <div class="tab-content" id="pills-tabContent">
    @foreach($tags as $tag)
        <div class="tab-pane fade {{ $loop->first ? ' show active' : '' }}" id="pills-home-{{ $tag->id }}" role="tabpanel" aria-labelledby="tag-home-{{ $tag->id }}">
            <div class="row">
                @foreach($tag->emisionsLimites(6) as $emision)
                    <x-big-w-card class="mb-3" :isForTagHome="true" :emision="$emision" />
                @endforeach
            </div>
        </div>
    @endforeach



    </div>
</div>
