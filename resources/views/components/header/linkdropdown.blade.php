<div class="dropdown">
    @vite(['resources/js/app.js'])
    <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $category->name }}
    </a>

    <ul class="dropdown-menu">
        @foreach($category->programmesOrderByHeight as $programme)
        <li><a class="dropdown-item card" href="#">{{ $programme->name }}</a></li>
        @endforeach
    </ul>
</div>
