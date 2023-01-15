<?php

namespace App\View\Components;

use App\Models\Programme;
use Illuminate\View\Component;

class SearchEngine extends Component
{

    public $programmesSearch;

    public $type;

    public $tagsSearch;

    public $duration;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->programmesSearch = Programme::allActiveEmisions()->get();
        $this->tagsSearch = \App\Models\Tag::all();
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
