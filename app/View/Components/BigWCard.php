<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class BigWCard extends Component
{

    protected Emision $emision;

    protected bool $isForTagHome;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($emision, $isForTagHome = false)
    {
        $this->emision = $emision;
        $this->isForTagHome = $isForTagHome;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cards.bid-w-card', [
            "emision" => $this->emision,
            "isForTagHome" => $this->isForTagHome
        ]);
    }
}
