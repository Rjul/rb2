<?php

namespace App\Livewire\V2;

use App\Livewire\V2\Concerns\WithEmissionFilters;
use App\Models\Emision;
use App\Models\Tag;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Hub / moteur du catalogue. /emissions (tout) et /emissions/{type}
 * (audio|video|articles) en sous-chemins self-canonical (SEO).
 * Par-dessus le type (porté par l'URL de chemin) : recherche plein-texte,
 * tri, filtres thèmes et durée (portés par la query, réactifs en AJAX).
 */
class EmissionsIndex extends TallPage
{
    use WithPagination;
    use WithEmissionFilters;

    /** Segment d'URL : audio|video|articles|null. */
    public ?string $type = null;

    /** Thèmes (ids de tags) et durée max (minutes). */
    #[Url(history: true)]
    public array $themes = [];

    #[Url(history: true)]
    public ?int $duration = null;

    private array $map = [
        'audio'    => ['label' => 'Audio',    'media' => Emision::TYPE_AUDIO],
        'video'    => ['label' => 'Vidéo',    'media' => Emision::TYPE_VIDEO],
        'articles' => ['label' => 'Articles', 'media' => Emision::TYPE_TEXT],
    ];

    public function mount(?string $type = null): void
    {
        $this->type = $type;
    }

    public function updatedThemes(): void
    {
        $this->resetPage();
    }

    public function updatedDuration(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'sort', 'themes', 'duration');
        $this->resetPage();
    }

    public function render()
    {
        $media = $this->type ? ($this->map[$this->type]['media'] ?? null) : null;

        $query = Emision::query()
            ->join('programmes', 'emisions.programme_id', '=', 'programmes.id')
            ->where('programmes.is_active', true)
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->select('emisions.*')
            ->with(['attachment', 'programme.group_programme']);

        $this->applyTextAndSort($query);

        if ($media) {
            $query->where('emisions.media_type', $media);
        }
        if ($this->themes) {
            $query->whereHas('tags', fn ($t) => $t->whereIn('tags.id', $this->themes));
        }
        if ($this->duration) {
            $query->where('emisions.duration', '<', $this->duration);
        }

        $emissions = $query->paginate(16)->withQueryString();

        // Onglets de type : on reporte la query courante (recherche/tri/filtres) sur les liens.
        $carry = http_build_query(request()->except(['page', 'type']));
        $suffix = $carry ? '?' . $carry : '';
        $tabs = [['key' => null, 'label' => 'Tout', 'url' => route('v2.emissions') . $suffix]];
        foreach ($this->map as $key => $info) {
            $tabs[] = ['key' => $key, 'label' => $info['label'], 'url' => route('v2.emissions.type', ['type' => $key]) . $suffix];
        }

        $label     = $this->type ? $this->map[$this->type]['label'] : null;
        $canonical = $this->type ? route('v2.emissions.type', ['type' => $this->type]) : route('v2.emissions');
        $hasFilters = trim($this->q) !== '' || $this->themes || $this->duration || $this->sort !== 'recent';

        $crumbs = array_values(array_filter([
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Émissions', 'url' => route('v2.emissions')],
            $label ? ['name' => $label] : null,
        ]));

        return view('livewire.v2.emissions-index', [
            'emissions'       => $emissions,
            'tabs'            => $tabs,
            'label'           => $label,
            'crumbs'          => $crumbs,
            'hasFilters'      => $hasFilters,
            'tagOptions'      => \App\Support\FrontCache::remember('tags:options', fn () => Tag::orderedByName()->get()),
            'durationOptions' => [15 => '– de 15 min', 30 => '– de 30 min', 60 => '– d’1 h'],
        ])->layout('layouts.tall', [
            'title'           => ($label ? $label . ' — ' : '') . 'Toutes les émissions — Radio Bastides',
            'metaDescription' => 'Toutes les émissions de Radio Bastides' . ($label ? ' — ' . $label : '') . ' : à écouter, lire et voir, en accès libre.',
            'canonical'       => $canonical,
            'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
        ]);
    }
}
