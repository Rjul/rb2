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
                            <label id="label-tag-{{ $type }}" class="label-search-facet-elms flex-center h-100 form-check fs-2 label btn btn-outline-secondary
                                        {{ in_array($type, request('type', [])) ? ' active border border-quadrary border-3' : '' }}
                                        " for="type-{{ $type }}"
                                   style="color: #131e25"
                            >
                                <input class="position-absolute check-box__elm-search-engin left right d-none" type="checkbox" name="type[]" value="{{ $type }}" id="type-{{ $type }}"
                                    {{ in_array($type, request('type', [])) ? 'checked' : '' }}
                                >
                                <div class="">{{--{{ $key }}--}}
                                @if($type === \App\Models\Emision::TYPE_AUDIO)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-music-note-beamed" viewBox="0 0 16 16">
                                        <path d="M6 13c0 1.105-1.12 2-2.5 2S1 14.105 1 13c0-1.104 1.12-2 2.5-2s2.5.896 2.5 2zm9-2c0 1.105-1.12 2-2.5 2s-2.5-.895-2.5-2 1.12-2 2.5-2 2.5.895 2.5 2z"/>
                                        <path fill-rule="evenodd" d="M14 11V2h1v9h-1zM6 3v10H5V3h1z"/>
                                        <path d="M5 2.905a1 1 0 0 1 .9-.995l8-.8a1 1 0 0 1 1.1.995V3L5 4V2.905z"/>
                                    </svg>
                                @elseif($type === \App\Models\Emision::TYPE_VIDEO)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-camera-reels" viewBox="0 0 16 16">
                                        <path d="M6 3a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM1 3a2 2 0 1 0 4 0 2 2 0 0 0-4 0z"/>
                                        <path d="M9 6h.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 7.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 16H2a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h7zm6 8.73V7.27l-3.5 1.555v4.35l3.5 1.556zM1 8v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1z"/>
                                        <path d="M9 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM7 3a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                    </svg>
                                @elseif($type === \App\Models\Emision::TYPE_TEXT)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-chat-left-text" viewBox="0 0 16 16">
                                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                        <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                    </svg>
                                @endif
                                </div>
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
        </div>
    </form>
</aside>
