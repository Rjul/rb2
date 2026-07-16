<?php

namespace App\Livewire\V2\Concerns;

use Livewire\Attributes\Url;

/**
 * Moteur de filtrage d'émissions partagé (catalogue + page programme).
 * La classe hôte doit utiliser WithPagination (pour resetPage()).
 * Facettes communes : recherche plein-texte + tri. Chaque page ajoute ses
 * propres facettes (type, thèmes, durée…) via ses #[Url] et son render.
 */
trait WithEmissionFilters
{
    #[Url(as: 'q', history: true)]
    public string $q = '';

    #[Url(history: true)]
    public string $sort = 'recent';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    /** Recherche plein-texte (chaque mot dans nom OU description) + tri. */
    protected function applyTextAndSort($query)
    {
        if (trim($this->q) !== '') {
            foreach (preg_split('/\s+/', trim($this->q)) as $word) {
                $query->where(fn ($sub) => $sub
                    ->where('emisions.name', 'LIKE', '%' . $word . '%')
                    ->orWhere('emisions.description', 'LIKE', '%' . $word . '%'));
            }
        }

        return match ($this->sort) {
            'ancien' => $query->reorder('emisions.active_at', 'asc'),
            'az'     => $query->reorder('emisions.name', 'asc'),
            default  => $query->reorder('emisions.active_at', 'desc'),
        };
    }

    public function sortOptions(): array
    {
        return [
            'recent' => 'Plus récentes',
            'ancien' => 'Plus anciennes',
            'az'     => 'Titre A → Z',
        ];
    }
}
