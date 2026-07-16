<?php

namespace App\Livewire\V2;

use App\Models\GroupProgramme;

/** Hub transversal : tous les programmes actifs, regroupés par catégorie. */
class ProgrammesIndex extends TallPage
{
    public function render()
    {
        $categories = \App\Support\FrontCache::remember('programmes:index', fn () => GroupProgramme::query()
            ->where('is_active', 1)
            ->orderBy('height')
            ->with(['programmesOrderByHeightAndActive' => fn ($q) => $q->with('group_programme')
                ->withCount(['emisions as emisions_count' => fn ($c) => $c->where('emisions.is_active', true)->where('emisions.active_at', '<', now())])])
            ->get()
            ->filter(fn ($c) => $c->programmesOrderByHeightAndActive->isNotEmpty()));

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Programmes'],
        ];

        return view('livewire.v2.programmes-index', compact('categories', 'crumbs'))
            ->layout('layouts.tall', [
                'title'           => 'Tous les programmes — Radio Bastides',
                'metaDescription' => 'Découvrez tous les programmes de Radio Bastides, classés par catégorie : chroniques, magazines, culture et musique du Lot-et-Garonne.',
                'canonical'       => route('v2.programmes'),
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
