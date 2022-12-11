<?php

namespace App\View\Components;

use App\Models\GroupProgramme;
use App\Models\WebsiteNew;
use Illuminate\View\Component;

class Tag extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public \App\Models\Tag $tag,
        public bool $isLarge = true
    ){}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cards.app-tag', [
            'tag'   => $this->tag,
            'isLarge' => $this->isLarge
        ]);
    }
}
