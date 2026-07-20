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
            $rel = ['attachment', 'programme.group_programme'];

            // Déduplication : une émission ne doit apparaître qu'UNE fois sur la page.
            // On mémorise les ids déjà affichés et chaque bloc pioche « les N suivants
            // pas encore vus », dans l'ordre où les blocs apparaissent à l'écran.
            $seen = [];
            $pick = function ($pool, int $n) use (&$seen) {
                $items = $pool->reject(fn ($e) => in_array($e->id, $seen, true))
                              ->take($n)->values();
                $seen = array_merge($seen, $items->pluck('id')->all());
                return $items;
            };

            // Pools volontairement larges pour absorber la déduplication sans vider les blocs.
            $recent     = Emision::getLast(14)->load($rel);
            $putForward = Emision::getLastALaUne(14)->load($rel);

            // 1) Héros = la plus récente. 2) Grille = les suivantes (plus le héros).
            $hero   = $pick($recent, 1)->first();
            $latest = $pick($recent, 7);

            // 3) Bandeau « à la une » = émissions cochées, hors déjà affichées.
            //    Repli si aucune (ou toutes déjà vues) : les récentes suivantes.
            $une = $pick($putForward, 4);
            if ($une->isEmpty()) {
                $une = $pick($recent, 4);
            }

            return [
                'hero'       => $hero,                       // grande carte immersive en tête
                'latest'     => $latest,                     // grille bento « dernières »
                'une'        => $une,                        // bandeau immersif « à la une »
                'programmes' => Programme::query()
                                    ->where('is_active', true)
                                    ->with('group_programme')
                                    ->withCount(['emisions as emisions_count' => fn ($q) => $q->where('emisions.is_active', true)->where('emisions.active_at', '<', now())])
                                    ->orderBy('height')
                                    ->orderBy('name') // départage à poids égal : alphabétique
                                    ->take(4)->get(),
                'tags'       => Tag::getQueryByOrderCountEmisions(6)->get(),
                // 4) Trios par type : les derniers de chaque type, hors déjà affichés.
                'audios'     => $pick(Emision::getLastByType('audio', 12)->load($rel), 3),
                'texts'      => $pick(Emision::getLastByType('text', 12)->load($rel), 3),
                'videos'     => $pick(Emision::getLastByType('video', 12)->load($rel), 3),
                // 5) Rendez-vous de la semaine : un « à la une » pas encore montré.
                'spotlight'  => $pick($putForward, 1)->first(),
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
