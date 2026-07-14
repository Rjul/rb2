<?php

namespace App\Livewire;

use App\Models\Emision;
use App\Models\Programme;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Page d'accueil (front TALL). Composant Livewire full-page : il ne fait que
 * réunir les données ; le rendu est délégué à des composants Blade segmentés
 * (sections + cartes). Requêtes reprises des View Components existants.
 */
#[Layout('layouts.tall')]
class HomePage extends Component
{
    public function render()
    {
        // Eager-load des pièces jointes → l'accesseur audioUrl() ne requête pas par carte.
        $une    = Emision::getLastALaUne()->load('attachment');
        $latest = Emision::getLast()->load('attachment');

        return view('livewire.home-page', [
            'latest'     => $latest,                     // grande carte + grille
            'une'        => $une,                        // bandeau immersif « à la une »
            'spotlight'  => $une->first(),               // rendez-vous de la semaine
            'programmes' => Programme::query()
                                ->where('is_active', true)
                                ->withCount('emisions')
                                ->orderBy('height')
                                ->take(4)->get(),
            'tags'       => Tag::getQueryByOrderCountEmisions(6)->get(),
            'audios'     => Emision::getLastByType('audio', 3)->load('attachment'),
            'texts'      => Emision::getLastByType('text', 3),
            'videos'     => Emision::getLastByType('video', 3),
        ]);
    }
}
