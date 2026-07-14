<?php

namespace App\Filament\Widgets;

use App\Models\Emision;
use Filament\Widgets\ChartWidget;

/**
 * Émissions publiées sur les 6 derniers mois (date de publication `active_at`).
 * Comptage par intervalle de dates → portable SQLite/MySQL.
 */
class EmisionsParMois extends ChartWidget
{
    protected static ?string $heading = 'Émissions publiées (6 derniers mois)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $labels = [];
        $valeurs = [];

        $mois = now()->startOfMonth()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $debut = $mois->copy()->startOfMonth();
            $fin = $mois->copy()->endOfMonth();

            $labels[] = ucfirst($debut->translatedFormat('M Y'));
            $valeurs[] = Emision::whereBetween('active_at', [$debut, $fin])->count();

            $mois->addMonth();
        }

        return [
            'datasets' => [[
                'label' => 'Émissions',
                'data' => $valeurs,
                'borderColor' => '#10b981',
                'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                'fill' => true,
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
