<div>
{{--    @dump($tags)--}}
<div>{{-- je voudrais un section avec des tabs clickables fait un bootstrap 5 please --}}

    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Onglet 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Onglet 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Onglet 3</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">Contenu de l'onglet 1</div>
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">Contenu de l'onglet 2</div>
                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">Contenu de l'onglet 3</div>
                </div>
            </div>
        </div>
    </div>


    @foreach($tags as $tag)
        {{ $tag->name }}

        @foreach($tag->emisionsLimites(6) as $emision)
            <x-big-w-card :emision="$emision" />

        @endforeach


    @endforeach
</div>
