<?php

namespace App\Livewire\V2;

use App\Models\Emision;
use App\Models\Programme;
use App\Models\Tag;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Recherche modernisée (facettes texte / type / thèmes / programmes / durée).
 * État porté par l'URL (#[Url]) pour le partage et le back/forward.
 * Reprend les critères du SearchController legacy, en corrigeant le groupement
 * des OR (facettes correctement isolées) et en passant les tags/programmes en OU.
 */
class SearchPage extends TallPage
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $q = '';

    #[Url(history: true)]
    public array $types = [];

    #[Url(history: true)]
    public array $tags = [];

    #[Url(history: true)]
    public array $programmes = [];

    #[Url(history: true)]
    public ?int $duration = null;

    /** Toute modification de filtre ramène à la page 1 (la pagination ne déclenche pas ce hook). */
    public function updated(): void
    {
        $this->resetPage();
    }

    public function toggleValue(string $prop, $value): void
    {
        $current = (array) $this->{$prop};
        $this->{$prop} = in_array($value, $current)
            ? array_values(array_filter($current, fn ($v) => $v != $value))
            : array_values([...$current, $value]);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'types', 'tags', 'programmes', 'duration');
        $this->resetPage();
    }

    public function render()
    {
        $query = Emision::query()
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->whereHas('programme', fn ($q) => $q->where('is_active', true))
            ->with(['attachment', 'programme.group_programme'])
            ->orderByDesc('emisions.active_at');

        // Texte : chaque mot doit apparaître dans le nom OU la description.
        if (trim($this->q) !== '') {
            foreach (preg_split('/\s+/', trim($this->q)) as $word) {
                $query->where(function ($sub) use ($word) {
                    $sub->where('emisions.name', 'LIKE', '%' . $word . '%')
                        ->orWhere('emisions.description', 'LIKE', '%' . $word . '%');
                });
            }
        }

        if ($this->types) {
            $query->whereIn('emisions.media_type', $this->types);
        }
        if ($this->programmes) {
            $query->whereIn('emisions.programme_id', $this->programmes);
        }
        if ($this->tags) {
            $query->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $this->tags));
        }
        if ($this->duration) {
            $query->where('emisions.duration', '<', $this->duration);
        }

        $emissions = $query->paginate(16)->withQueryString();

        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Recherche'],
        ];

        return view('livewire.v2.search-page', [
            'emissions'      => $emissions,
            'crumbs'         => $crumbs,
            'typeOptions'    => ['audio' => 'Audio', 'video' => 'Vidéo', 'text' => 'Article'],
            'durationOptions' => [15 => 'Moins de 15 min', 30 => 'Moins de 30 min', 60 => 'Moins d’1 h'],
            'tagOptions'     => \App\Support\FrontCache::remember('tags:options', fn () => Tag::orderedByName()->get()),
            'programmeOptions' => \App\Support\FrontCache::remember('programmes:options', fn () => Programme::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])),
            'hasFilters'     => trim($this->q) !== '' || $this->types || $this->tags || $this->programmes || $this->duration,
        ])->layout('layouts.tall', [
            'title'           => 'Recherche — Radio Bastides',
            'metaDescription' => 'Recherchez parmi toutes les émissions, articles et vidéos de Radio Bastides.',
            'canonical'       => route('v2.search'),
            'robots'          => 'noindex, follow',
        ]);
    }
}
