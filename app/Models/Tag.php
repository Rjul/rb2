<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Filters\Filterable;


class Tag extends \Spatie\Tags\Tag
{
    use HasFactory, Filterable;

    public function emisions() {
        return $this->morphedByMany(Emision::class, 'taggable');
    }

    public function emisionsLimites($limite = 10) {
        return $this->emisions()->limit($limite)->get();
    }

    /**
     * Émissions PUBLIÉES du thème (actives, datées, programme actif), eager-loadées
     * pour les cartes du front v2. Évite d'afficher des liens morts (404) vers du non-publié.
     */
    public function publishedEmisions(int $limite = 6)
    {
        // Identique pour tous les visiteurs → cache front (invalidé aux écritures BO).
        return \App\Support\FrontCache::remember("tag:{$this->id}:published:{$limite}", fn () => $this->emisions()
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->whereHas('programme', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('emisions.active_at')
            ->with(['attachment', 'programme.group_programme'])
            ->limit($limite)
            ->get());
    }

    public static function getQueryByOrderCountEmisions(int $limit): Builder
    {
        return self::withCount('emisions')->orderBy("emisions_count", "DESC")->limit($limit);
    }

    public function scopeOrderedByName(\Illuminate\Database\Eloquent\Builder $builder): Builder
    {
        return $builder->reorder('name');
    }

}
