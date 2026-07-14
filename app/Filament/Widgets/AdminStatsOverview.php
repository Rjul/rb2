<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Emision;
use App\Models\Programme;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Vue d'ensemble du back-office : chiffres réels de l'application
 * (remplace les widgets de démonstration d'Orchid).
 */
class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $enAttente = Comment::where('approved', false)->count();

        return [
            Stat::make('Émissions', Emision::count())
                ->description('Toutes émissions confondues')
                ->descriptionIcon('heroicon-m-microphone')
                ->color('primary'),

            Stat::make('Programmes actifs', Programme::where('is_active', true)->count())
                ->description('Programmes visibles sur le site')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('success'),

            Stat::make('Commentaires à modérer', $enAttente)
                ->description($enAttente > 0 ? 'En attente d’approbation' : 'Rien à modérer')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($enAttente > 0 ? 'warning' : 'gray'),

            Stat::make('Utilisateurs', User::count())
                ->description('Comptes enregistrés')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
