<section class="bg-primary wavy-box text-white relative">
    <div class="container relative">
        <span class="home-prog_big-title">Au programme</span>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-lg-5">
                        @foreach($emisions as $emision)
                            <a class="d-block text-end text-secondary home-prog_big_item " href="#">{{ $emision->programme->name }}</a>
                        @endforeach
                    </div>
                    <div class="col-lg-5">
                        @foreach($emisions as $emision)
                            <a class="d-block text-start text-white home-prog_big_item strong" href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}">{{ $emision->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <x-xl-w-card :$emision ></x-xl-w-card>
            </div>
        </div>
    </div>
</section>
