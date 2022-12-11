<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class HomeProgramme extends Component
{

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.blocks.home-programme', [
            "emisions" => Emision::getLastALaUne()
        ]);
    }
}
