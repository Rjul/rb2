<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class LastHome extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $type)
    {
        $this->lastHomeAudio = Emision::getLastByType($type, 3);
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.blocks.last-home', [
            'lastHomeAudio' => $this->lastHomeAudio,
            'type'          => $this->type
        ]);
    }
}
