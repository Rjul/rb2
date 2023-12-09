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

    public static function getQueryByOrderCountEmisions(int $limit): Builder
    {
        return self::withCount('emisions')->orderBy("emisions_count", "DESC")->limit($limit);
    }

    public function scopeOrderedByName(\Illuminate\Database\Eloquent\Builder $builder): Builder
    {
        return $builder->reorder('name');
    }

}
