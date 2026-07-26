<?php

namespace App\Providers;

use App\Support\FrontCache;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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

        // Transport mail « brevo » via l'API HTTPS (port 443) : le SMTP sortant
        // est bloqué sur l'hébergement OVH mutualisé. Activé par MAIL_MAILER=brevo.
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory(null, HttpClient::create()))->create(
                new Dsn('brevo+api', 'default', config('services.brevo.key'))
            );
        });

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
