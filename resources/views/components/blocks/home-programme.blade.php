<section class="bg-primary wavy-box text-white relative">
    <div class="container relative" id="homepage-programme-collapse">
        <span class="home-prog_big-title">Au programme</span>
        <div class="row">
            <div class="col-12 col-lg-6">
                @foreach($emisions as $emision)
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseCard{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="#multiCollapseCard{{ $loop->index }}">
                        <a class="d-block text-end text-secondary home-prog_big_item " href="#">{{ $emision->programme->name }}</a>
                        <a class="d-block text-start text-white home-prog_big_item strong" href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}">{{ $emision->name }}</a>
                    </button>
                @endforeach
            </div>

            <div class="col-12 col-lg-6">
                @foreach($emisions as $emision)
                    <div class="collapse{{ $loop->first ? ' show' : '' }}" data-bs-parent="#homepage-programme-collapse" id="multiCollapseCard{{ $loop->index }}">
                        <x-xl-w-card :emision="$emision" ></x-xl-w-card>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</section>
