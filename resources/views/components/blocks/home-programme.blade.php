<section class="wavy-box text-white relative ">
    <svg class="lower-margin" version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 1920 156" style="enable-background:new 0 0 1920 156;" xml:space="preserve">
        <style type="text/css">
            .st0{fill:#001C41;}
        </style>
        <path class="st0" d="M1919.78,8.52v147.83H0.22V92.39c148.27-38.43,345.06-82.89,475.66-87c12.78-0.4,24.93-0.41,36.34,0
        c220,8,504,108,716,108C1414.96,113.39,1732.06,26.49,1919.78,8.52z"/>
    </svg>
    <div style="background-color: #001C41">
        <div class="container" id="homepage-programme-collapse">

            <span class="home-prog_big-title">Au programme</span>
            <div class="row">
                <div class="col-12 col-xl-6">
                    <div class="row m-3">
                    @foreach($emisions as $emision)
                        <button class="btn btn-sm btn-primary row-cols-xl-1 rounded-5 mt-5 mb-5 btn-home_tag_card-selector {{ $loop->first ? 'selected' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseCard{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="#multiCollapseCard{{ $loop->index }}">
                            <a class="text-start text-secondary home-prog_big_item href="{{ route('list-programme', [ 'programme' => $emision->programme ]) }}" >{{ $emision->programme->name }}</a>
                            <a class=" text-end text-white home-prog_big_item strong" href="{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}">{{ $emision->name }}</a>
                        </button>
                    @endforeach
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    @foreach($emisions as $emision)
                        <div class="collapse card-home_tag_card-to-display{{ $loop->first ? ' show' : '' }}" data-bs-parent="#homepage-programme-collapse" id="multiCollapseCard{{ $loop->index }}">
                            <x-xl-w-card :emision="$emision" ></x-xl-w-card>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    <svg class="lower-margin" version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 1920 345" style="enable-background:new 0 0 1920 345;" xml:space="preserve">
        <style type="text/css">
            .st0{fill:#001C41;}
        </style>
        <path class="st0" d="M1920,0v344.55C1787.85,195.9,1473.64-11.12,764.44,44.04c-380.7,29.61-617.26,57.73-764,81.73V0H1920z"/>
    </svg>
</section>
