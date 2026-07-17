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

    /**
     * Tri alphabétique insensible à la casse sur le nom traduit.
     * `name` est un JSON traduisible ({"fr": …}) : un orderBy brut trierait la
     * chaîne JSON (majuscules avant minuscules en binaire), d'où le lower()
     * sur la valeur extraite — portable MySQL (prod) / SQLite (tests).
     */
    public function scopeOrderByName(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $wrapped = $query->getQuery()->getGrammar()->wrap('name->'.app()->getLocale());
        $expr = "lower({$wrapped})";

        // En MySQL (prod), la collation Unicode classe aussi les accents
        // (Économie avec les E) ; SQLite (tests) ne la connaît pas.
        if ($query->getConnection()->getDriverName() === 'mysql') {
            $expr .= ' COLLATE utf8mb4_unicode_ci';
        }

        return $query->orderByRaw($expr);
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
