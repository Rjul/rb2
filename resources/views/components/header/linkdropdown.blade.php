<li class="nav-item">
    <a class="nav-link dropdown-toggle p-2 m-3 m-xl-5" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $category->name }}
    </a>
    <div class="dropdown-menu mt-0" aria-labelledby="navbarDropdown" style="
                          border-top-left-radius: 0;
                          border-top-right-radius: 0;
                          width: 100vw;
                          position: absolute;
                          left: 0px;
                          overflow-x: scroll;
                          overflow-y: scroll;
                        ">
        <div class="container">
            <div class="row my-4">
                @foreach($category->programmesOrderByHeightAndActive as $programme)
                <div class="col-6 col-md-6 col-lg-3 mb-3 mb-lg-0">
                    <div class="list-group list-group-flush h-100">
                        <a class="border-bottom fs-2 mb-3 lh-base h-100 list-group-item-action text"
                           href="{{ route('list-programme', $programme) }}">{{ $programme->name }}</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</li>
