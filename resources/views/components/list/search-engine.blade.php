<aside class="w-md-25 w-25">
    <form action="{{ route('list-search') }}" method="get" class="d-flex flex-column">
        <input type="text" name="q" placeholder="Search" value="{{ request('query') }}">

        <div class="accordion" id="accordionPanelsStayOpenExample">
            <div class="accordion-item">
                <h2 class="accordion-header d-block">
                    <button class="accordion-button fs-2" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-programmes" aria-expanded="true" aria-controls="panelsStayOpen-programmes">
                        Nos Programmes
                    </button>
                </h2>
                <div id="panelsStayOpen-programmes" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-programmes">
                    <div class="accordion-body">
                        <div class="form-check d-flex flex-wrap">
                            @foreach($programmesSearch as $programmeSearch)
                                <div class="position-relative ">
                                    <label id="label-programme-{{ $programmeSearch->id }}" class="form-check label btn btn-secondary btn--small
                                        {{ in_array($programmeSearch->id, request('programmes', [])) ? 'active' :
                                                ($programmeSearch->id === request('programme', new \App\Models\Programme() )->id ?? false  ? 'active' : '') }}
                                        " for="programme-{{ $programmeSearch->id }}"
                                        >
                                        <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="programmes[]" value="{{ $programmeSearch->id }}" id="programme-{{ $programmeSearch->id }}"
                                            {{ in_array($programmeSearch->id, request('programmes', [])) ? 'checked' :
                                                ($programmeSearch->id === request('programme', new \App\Models\Programme() )->id ?? false  ? 'checked' : '') }}
                                        >
                                        {{ $programmeSearch->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-heading__themes">
                    <button class="accordion-button fs-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-themes" aria-expanded="false" aria-controls="panelsStayOpen-themes">
                        Nos thèmes
                    </button>
                </h2>
                <div id="panelsStayOpen-themes" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-themes">
                    <div class="accordion-body">
                        <div class="form-check d-flex flex-wrap">
                            @foreach($tagsSearch as $tagSearch)
                                <div class="position-relative ">
                                    <label id="label-tag-{{ $tagSearch->id }}" class="form-check label btn btn-secondary btn--small
                                        {{ in_array($tagSearch->id, request('tags', [])) ? 'active' :
                                                ($tagSearch->slug === request('tag', new \App\Models\Tag() ) ?? false  ? 'active' : '') }}
                                        " for="tag-{{ $tagSearch->id }}"
                                    >
                                        <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="tags[]" value="{{ $tagSearch->id }}" id="tag-{{ $tagSearch->id }}"
                                            {{ in_array($tagSearch->id, request('tags', [])) ? 'checked' :
                                                ($tagSearch->slug === request('tag', new \App\Models\Tag() ) ?? false  ? 'checked' : '') }}
                                        >
                                        {{ $tagSearch->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                        Accordion Item #3
                    </button>
                </h2>
                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                    <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                </div>
            </div>
        </div>

        <button type="submit">Search</button>
    </form>
</aside>
