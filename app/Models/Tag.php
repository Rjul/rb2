<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Tag extends \Spatie\Tags\Tag
{
    use HasFactory;

    public function emisions() {
        return $this->morphedByMany(Emision::class, 'taggable');
    }

    public static function getQueryByOrderCountEmisions(int $limit): Builder
    {
        return self::withCount('emisions')->orderBy("emisions_count", "DESC")->limit(10);
    }
}
