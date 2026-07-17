<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * @param  string  $title  Titre d'onglet, passé via <x-guest-layout title="…"> ;
     *                         exposé comme variable $title dans layouts.guest.
     */
    public function __construct(public string $title = 'Espace membre')
    {
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
