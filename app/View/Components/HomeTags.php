<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class HomeTags extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tags = \App\Models\Tag::getQueryByOrderCountEmisions(8)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.blocks.home-tags', [
            "tags" => $this->tags
        ]);
    }
}
