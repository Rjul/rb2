<?php

namespace App\View\Components;

use App\Models\Emision;
use App\Models\Programme;
use Illuminate\View\Component;

class SearchEngine extends Component
{

    public $programmesSearch;

    public $types;

    public $tagsSearch;

    public $durations;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->programmesSearch = Programme::allActiveEmisions()->get();
        $this->tagsSearch = \App\Models\Tag::orderedByName()->get();
        $this->types = [
            'Audio' => Emision::TYPE_AUDIO,
            'Video' => Emision::TYPE_VIDEO,
            'Article' => Emision::TYPE_TEXT
        ];
        $this->durations = [
            'Moins de 5 minutes' => 5.01,
            'Moins de 15 minutes' => 15.01,
            'Moins de 30 minutes' => 30.01
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.list.search-engine');
    }
}
