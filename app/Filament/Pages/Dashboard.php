<?php

namespace App\Filament\Pages;

use App\Support\FrontCache;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * Dashboard du back-office. Ajoute une action « Vider le cache du site » :
 * force la régénération du cache des pages publiques (accueil, catégories,
 * thèmes, options de filtres…) sans attendre l'expiration horaire.
 */
class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('flushFrontCache')
                ->label('Vider le cache du site')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Vider le cache du site')
                ->modalDescription("Régénère immédiatement le cache des pages publiques. Utile si une modification n'apparaît pas tout de suite en ligne.")
                ->modalSubmitActionLabel('Vider le cache')
                ->action(function () {
                    FrontCache::bump();

                    Notification::make()
                        ->title('Cache du site vidé')
                        ->body('Les pages publiques seront régénérées à la prochaine visite.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
