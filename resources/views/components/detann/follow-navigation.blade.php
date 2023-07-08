<section class="detann__down_image mt-5">
    <h2 class="m-3 h1">Poursuivre parmi les émissions du programme <u>{{ $emision->programme->name }}</u></h2>
    <div class="d-flex w-100 flex-row justify-content-around align-items-center">
        @if($next)
        <div class="detann__next-and-preview">
            <a href="{{ route('view-emision', [ 'programme' => $next->programme, 'emision' => $next ]) }}">
                <h3 class="detann__next_title h2">{{ $next->name }}</h3>
                <svg class="svg__flech svg__flech_reversed" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 1080">
                    <defs>
                        <style>
                            .cls-1 {
                                fill: #000000;
                            }
                        </style>
                    </defs>
                    <path id="Tracé_11" data-name="Tracé 11" class="cls-1" d="M342.89,770.24l-3.05-460.48,400.31,227.61-397.26,232.87Z"/>
                </svg>
            </a>
        </div>
        @endif
        @if($before)
        <div class="detann__next-and-preview">
            <a href="{{ route('view-emision', [ 'programme' => $before->programme, 'emision' => $before ]) }}">
                <h3 class="detann__preview_title h2">{{ $before->name }}</h3>
                <svg class="svg__flech" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 1080">
                    <defs>
                        <style>
                            .cls-1 {
                                fill: #000000;
                            }
                        </style>
                    </defs>
                    <path id="Tracé_11" data-name="Tracé 11" class="cls-1" d="M342.89,770.24l-3.05-460.48,400.31,227.61-397.26,232.87Z"/>
                </svg>
            </a>
        </div>
        @endif
    </div>
</section>
