<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Orchid\Access\Impersonation;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('gestion')
            ->path('gestion')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\AdminStatsOverview::class,
                \App\Filament\Widgets\EmisionsParMois::class,
            ])
            // Bandeau d'usurpation d'identité : visible tant qu'on est « connecté en tant que ».
            ->renderHook(
                PanelsRenderHook::BODY_START,
                function (): string {
                    if (! Impersonation::isSwitch()) {
                        return '';
                    }

                    $name = e(auth()->user()?->name ?? '');
                    $url = route('impersonation.leave');

                    return <<<HTML
                        <div style="background:#f59e0b;color:#111827;padding:.55rem 1rem;display:flex;flex-wrap:wrap;gap:.75rem;justify-content:center;align-items:center;font-size:.875rem;line-height:1.2;">
                            <span>Vous êtes connecté en tant que <strong>{$name}</strong>.</span>
                            <a href="{$url}" style="font-weight:700;text-decoration:underline;">Revenir à mon compte</a>
                        </div>
                        HTML;
                }
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
