<?php

namespace App\Providers;

use App\Support\FrontCache;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Directive @comments(['model' => $model]) — remplace le paquet laravelista/comments
        Blade::include('comments.index', 'comments');

        // Invalidation du cache front : toute écriture BO sur un modèle affiché
        // publiquement fait tourner la version des clés (voir FrontCache).
        foreach ([
            \App\Models\Emision::class,
            \App\Models\Programme::class,
            \App\Models\GroupProgramme::class,
            \App\Models\Tag::class,
        ] as $model) {
            $model::saved(fn () => FrontCache::bump());
            $model::deleted(fn () => FrontCache::bump());
        }
    }
}
