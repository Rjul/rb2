<aside class="w-md-25 w-25">
    @push('scripts')
        @vite(['resources/js/list/search.js'])
    @endpush
    <form id="searchEngineForm" action="{{ route('list-search') }}" method="get" class="d-flex flex-column">
        <div class="accordion" id="accordionPanelsStayOpenExample">
            <div class="accordion-item">
                <div class="d-flex flex-wrap justify-content-around mt-3">
                    @foreach($types as $key => $type)
                        <div class="position-relative m-1 ">
                            <label id="label-tag-{{ $type }}" class="label-search-facet-elms flex-center h-100 form-check fs-4 label btn btn-primary btn--small
                                        {{ in_array($type, request('type', [])) ? ' active border border-quadrary border-3' : '' }}
                                        " for="type-{{ $type }}"
                            >
                                <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="type[]" value="{{ $type }}" id="type-{{ $type }}"
                                    {{ in_array($type, request('type', [])) ? 'checked' : '' }}
                                >
                                <div class="">{{ $key }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-heading__themes">
                        <button class="accordion-button fs-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-themes" aria-expanded="true" aria-controls="panelsStayOpen-themes">
                            Nos thèmes
                        </button>
                    </h2>
                    <div id="panelsStayOpen-themes" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-themes">
                        <div class="accordion-body">
                            <div class="d-flex flex-wrap">
                                @foreach($tagsSearch as $tagSearch)
                                    <div class="position-relative m-1 ">
                                        <label id="label-tag-{{ $tagSearch->id }}" class="label-search-facet-elms flex-center h-100 form-check fs-4 label btn btn-primary btn--small
                                            {{ in_array($tagSearch->id, request('tags_id', [])) ? ' active border border-success border-3' :
                                                    ($tagSearch->slug === request('tag', new \App\Models\Tag() ) ?? false  ? ' active border border-success' : '') }}
                                            " for="tag-{{ $tagSearch->id }}"
                                               style="background-color: {{ $tagSearch->color }}"
                                        >
                                            <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="tags_id[]" value="{{ $tagSearch->id }}" id="tag-{{ $tagSearch->id }}"
                                                {{ in_array($tagSearch->id, request('tags_id', [])) ? 'checked' :
                                                    ($tagSearch->slug === request('tag', new \App\Models\Tag() ) ?? false  ? 'checked' : '') }}
                                            >
                                            <div class="">{{ $tagSearch->name }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>



                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fs-1" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-programmes" aria-expanded="{{ request('programmes_id', []) ? 'true' : 'false' }}" aria-controls="panelsStayOpen-programmes">
                            Nos Programmes
                        </button>
                    </h2>
                    <div id="panelsStayOpen-programmes" class="accordion-collapse collapse {{ request('programmes_id', []) ? 'show' : '' }}" aria-labelledby="panelsStayOpen-programmes">
                        <div class="accordion-body">
                            <div class="d-flex flex-wrap">
                                @foreach($programmesSearch as $programmeSearch)
                                    <div class="position-relative m-1 ">
                                        <label id="label-programme-{{ $programmeSearch->id }}" class="label-search-facet-elms h-100 form-check fs-6 label btn  btn-primary btn--small
                                            {{ in_array($programmeSearch->id, request('programmes_id', [])) ? 'active btn-secondary' :
                                                    ($programmeSearch->id === request('programme', new \App\Models\Programme() )->id ?? false  ? 'active btn-secondary' : '') }}
                                            " for="programme-{{ $programmeSearch->id }}"
                                            >
                                            <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="programmes_id[]" value="{{ $programmeSearch->id }}" id="programme-{{ $programmeSearch->id }}"
                                                {{ in_array($programmeSearch->id, request('programmes_id', [])) ? 'checked' :
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

            <h2 class="accordion-header" id="panelsStayOpen-heading__duration">
                <button class="accordion-button fs-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-duration" aria-expanded="true" aria-controls="panelsStayOpen-duration">
                    Durée
                </button>
            </h2>
            <div id="panelsStayOpen-duration" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-duration">
                <div class="accordion-body">
                    <div class="d-flex flex-wrap">
                        @foreach($durations as $key => $duration)
                            <div class="position-relative m-1 ">
                                <label id="label-tag-{{ $duration }}" class="label-search-facet-elms flex-center h-100 form-check fs-4 label btn btn-primary btn--small
                                        {{ in_array($duration, request('duration', [])) ? ' active border border-success border-3' : '' }}
                                        " for="duration-{{ $duration }}"
                                >
                                    <input class="position-absolute check-box__elm-search-engin check-box__elm-duration-search-engin left right d-none" type="checkbox" name="duration[]" value="{{ $duration }}" id="duration-{{ $duration }}"
                                        {{ in_array($duration, request('duration', [])) ? 'checked' : '' }}
                                    >
                                    <div class="">{{ $key }}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>

    </form>
</aside>
