<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class WebsiteNew extends Model
{
    use HasFactory;
    use AsSource, Attachable, Filterable;

    /**
     * Return active GroupProgrammes
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getActive() {
        return self::query()
            ->where('active', '=', 1)
            ->where('active_at', '<', new \DateTime())
            ->where('end_at', '>', new \DateTime())
            ->get();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'content',
        'active',
        'active_at',
        'end_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected array $allowedFilters = [
        'name',
        'content',
        'active',
        'active_at',
        'end_at',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected array $allowedSorts = [
        'name',
        'content',
        'active',
        'active_at',
        'end_at',
    ];
}
