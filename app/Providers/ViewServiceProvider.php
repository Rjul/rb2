<?php

namespace App\Providers;

use App\View\Components\BigWCard;
use App\View\Components\LastHome;
use App\View\Components\SmallCard;
use App\View\Composers\HeaderComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewService extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('last-home', LastHome::class);
        Blade::component('big-w-card', BigWCard::class);
        Blade::component('small-card', SmallCard::class);
    }
}
