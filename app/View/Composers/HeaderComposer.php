<?php

namespace App\View\Composers;

use App\Models\GroupProgramme;
use Illuminate\View\Component;
use Illuminate\Support\Facades\View;

class HeaderComposer
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(GroupProgramme $groupProgramme)
    {
        $this->groupProgrammes = GroupProgramme::getActiveOrderByHeight();
    }

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('groupProgrammes', $this->groupProgrammes);
    }
}
