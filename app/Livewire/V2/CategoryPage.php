<?php

namespace App\Livewire\V2;

use App\Models\Emision;
use App\Models\GroupProgramme;

/** Page catégorie : ses programmes + ses dernières émissions. */
class CategoryPage extends TallPage
{
    public GroupProgramme $category;

    public function mount(string $categorie): void
    {
        // Décision produit : slug catégorie sans id → résolution stricte par slug.
        $this->category = GroupProgramme::query()->where('slug', $categorie)->firstOrFail();
    }

    public function render()
    {
        $programmes = \App\Support\FrontCache::remember("category:{$this->category->id}:programmes", fn () => $this->category->programmesOrderByHeightAndActive()
            ->with('group_programme')
            ->withCount(['emisions as emisions_count' => fn ($q) => $q->where('emisions.is_active', true)->where('emisions.active_at', '<', now())])
            ->get());

        $emissions = \App\Support\FrontCache::remember("category:{$this->category->id}:latest", fn () => Emision::query()
            ->join('programmes', 'emisions.programme_id', '=', 'programmes.id')
            ->where('programmes.group_programme_id', $this->category->id)
            ->where('programmes.is_active', true)
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->select('emisions.*')
            ->orderByDesc('emisions.active_at')
            ->orderBy('programmes.height')
            ->with(['attachment', 'programme.group_programme'])
            ->limit(8)
            ->get());

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Catégories', 'url' => route('v2.categories')],
            ['name' => $this->category->name],
        ];

        $desc = trim(strip_tags((string) $this->category->description));

        return view('livewire.v2.category-page', compact('programmes', 'emissions', 'crumbs'))
            ->layout('layouts.tall', [
                'title'           => $this->category->name . ' — Radio Bastides',
                'metaDescription' => \Illuminate\Support\Str::limit($desc !== '' ? $desc : 'Les programmes et émissions de la catégorie ' . $this->category->name . ' sur Radio Bastides.', 160),
                'canonical'       => $this->category->canonicalUrl(),
                'ogImage'         => $this->category->image ?: null,
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
