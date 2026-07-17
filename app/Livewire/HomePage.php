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
        // Ces listes sont identiques pour tous les visiteurs → cache front,
        // invalidé à chaque écriture BO (cf. App\Support\FrontCache).
        $data = \App\Support\FrontCache::remember('home:data', function () {
            // Eager-load : `attachment` (audioUrl sans N+1) + `programme.group_programme`
            // (les cartes appellent canonicalUrl() qui remonte jusqu'à la catégorie).
            $rel    = ['attachment', 'programme.group_programme'];
            $latest = Emision::getLast()->load($rel);
            $une    = Emision::getLastALaUne()->load($rel);

            // Repli : si aucune émission n'est cochée « à la une » (is_put_forward),
            // on prend les 4 dernières publiées → le bandeau n'est jamais vide.
            // Réutilise $latest (déjà chargé) : pas de requête supplémentaire.
            if ($une->isEmpty()) {
                $une = $latest->take(4)->values();
            }

            return [
                'latest'     => $latest,                     // grande carte + grille
                'une'        => $une,                        // bandeau immersif « à la une »
                'spotlight'  => $une->first(),               // rendez-vous de la semaine
                'programmes' => Programme::query()
                                    ->where('is_active', true)
                                    ->with('group_programme')
                                    ->withCount(['emisions as emisions_count' => fn ($q) => $q->where('emisions.is_active', true)->where('emisions.active_at', '<', now())])
                                    ->orderBy('height')
                                    ->take(4)->get(),
                'tags'       => Tag::getQueryByOrderCountEmisions(6)->get(),
                'audios'     => Emision::getLastByType('audio', 3)->load($rel),
                'texts'      => Emision::getLastByType('text', 3)->load($rel),
                'videos'     => Emision::getLastByType('video', 3)->load($rel),
            ];
        });

        return view('livewire.home-page', $data)->layout('layouts.tall', [
            'title'           => 'Radio Bastides — la radio associative de Villeneuve-sur-Lot',
            'metaDescription' => 'Radio Bastides, la radio associative de Villeneuve-sur-Lot et du Lot-et-Garonne : émissions, chroniques et musiques d’ici, à écouter où et quand vous voulez.',
            'canonical'       => route('v2.home'),
            'ogImage'         => url('/imgs/logo.png'),
        ]);
    }
}
