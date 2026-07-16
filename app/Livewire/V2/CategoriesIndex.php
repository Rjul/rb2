<?php

namespace App\Livewire\V2;

use App\Models\GroupProgramme;

/** Hub : toutes les catégories actives (GroupProgramme). */
class CategoriesIndex extends TallPage
{
    public function render()
    {
        $categories = \App\Support\FrontCache::remember('categories:index', fn () => GroupProgramme::query()
            ->where('is_active', 1)
            ->withCount(['programmes as programmes_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('height')
            ->get());

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Catégories'],
        ];

        return view('livewire.v2.categories-index', compact('categories', 'crumbs'))
            ->layout('layouts.tall', [
                'title'           => 'Toutes les catégories — Radio Bastides',
                'metaDescription' => 'Explorez les catégories de Radio Bastides et parcourez tous les programmes de la radio associative de Villeneuve-sur-Lot.',
                'canonical'       => route('v2.categories'),
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
