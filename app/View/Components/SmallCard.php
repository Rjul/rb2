<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class SmallCard extends Component
{
    protected Emision $emision;

    protected bool $suggestion = false;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($emision, $suggestion = false)
    {
        $this->emision = $emision;
        $this->suggestion = $suggestion;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cards.small-card', [
            "emision" => $this->emision,
            "suggestion" => $this->suggestion
        ]);
    }
}
