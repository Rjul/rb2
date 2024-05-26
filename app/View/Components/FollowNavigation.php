<?php

namespace App\View\Components;

use App\Models\Emision;
use Illuminate\View\Component;

class FollowNavigation extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Emision $emision)
    {
        $this->before = $emision::query()
            ->where('active_at',"<" , $emision->active_at )
            ->where('programme_id', "=", $emision->programme->id )
            ->where('active_at', '<', now())
            ->orderBy('active_at', 'desc')
            ->first();
        $this->next = $emision::query()
            ->where('active_at','>' , $emision->active_at)
            ->where('programme_id', "=", $emision->programme->id )
            ->where('active_at', '<', now())
            ->orderBy('active_at')
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detann.follow-navigation',[
            'before' => $this->before,
            'next' => $this->next,
        ]);
    }
}
