<div class="modal fade" id="exampleModal" tabindex="99" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="m-3 d-flex align-items-end">
                    <label class="modal-title" id="exampleModalLabel">Recherche</label>
                    <input id="input-suggestion" type="text" class="form-control-search form-control m-3" value="{{ request('query') }}">
                    <button class="m-3 btn btn-primary pe-auto"><i id="search-btn" class="fa fa-search ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </i>
                    </button>

                </div>
                <button type="button" class="btn-close p-3" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">

                <div id="place-suggestion" class="p-3">

                </div>
            </div>
        </div>
    </div>
</div>
