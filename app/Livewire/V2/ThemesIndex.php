<?php

namespace App\Livewire\V2;

use App\Models\Tag;

/** Hub : tous les thèmes (tags) ayant des émissions. */
class ThemesIndex extends TallPage
{
    public function render()
    {
        // Compte uniquement les émissions publiées (actives, datées, programme actif)
        // pour aligner le badge avec ce que la page thème listera réellement.
        // Requête la plus lourde du front (agrégat sur toutes les émissions) → cache.
        $tags = \App\Support\FrontCache::remember('themes:index', fn () => Tag::withCount(['emisions as emisions_count' => fn ($q) => $q
                ->where('emisions.is_active', true)
                ->where('emisions.active_at', '<', now())
                ->whereHas('programme', fn ($p) => $p->where('is_active', true))])
            ->orderBy('emisions_count', 'desc')
            ->get()
            ->filter(fn ($t) => $t->emisions_count > 0));

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Thèmes'],
        ];

        return view('livewire.v2.themes-index', compact('tags', 'crumbs'))
            ->layout('layouts.tall', [
                'title'           => 'Tous les thèmes — Radio Bastides',
                'metaDescription' => 'Explorez les émissions de Radio Bastides par thème et retrouvez les sujets qui vous intéressent.',
                'canonical'       => route('v2.themes'),
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
