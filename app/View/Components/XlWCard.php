<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class XlWCard extends Component
{

    protected Emision $emision;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($emision)
    {
        $this->emision = $emision;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cards.xl-w-card', [
            "emision" => $this->emision,
        ]);
    }
}
