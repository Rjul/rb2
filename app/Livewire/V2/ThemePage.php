<?php

namespace App\Livewire\V2;

use App\Models\Tag;
use Livewire\WithPagination;

/** Page thème : les émissions taguées, paginées (facette transversale). */
class ThemePage extends TallPage
{
    use WithPagination;

    public Tag $theme;

    public function mount(string $tag): void
    {
        // Résolution par le slug traduit (fr), comme le legacy /thematique-{tag}.
        $t = Tag::query()->where('slug->fr', $tag)->first();
        abort_if(! $t, 404);
        $this->theme = $t;
    }

    public function render()
    {
        $emissions = $this->theme->emisions()
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->whereHas('programme', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('emisions.active_at')
            ->with(['attachment', 'programme.group_programme'])
            ->paginate(16)
            ->withQueryString();

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Thèmes', 'url' => route('v2.themes')],
            ['name' => $this->theme->name],
        ];

        return view('livewire.v2.theme-page', compact('emissions', 'crumbs'))
            ->layout('layouts.tall', [
                'title'           => $this->theme->name . ' — Thème — Radio Bastides',
                'metaDescription' => 'Toutes les émissions de Radio Bastides sur le thème « ' . $this->theme->name . ' ».',
                'canonical'       => route('v2.theme', ['tag' => $this->theme->slug]),
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
