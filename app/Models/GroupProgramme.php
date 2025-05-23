<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class GroupProgramme extends Model
{
    use HasFactory, HasSlug;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'image',
        'is_active',
        'height'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

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
    protected $allowedFilters = [
        'id',
        'name',
        'is_active',
        'created_at',
        'height'
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'is_active',
        'created_at',
        'height'
    ];

    /**
     * Return active GroupProgrammes
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getActive() {
        return self::query()
            ->where('is_active', '=', 1)
            ->get();
    }

    /**
     * Return active groupProgrammes Ordered
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveOrderByHeight() {
        return self::query()
            ->where('is_active', '=', 1)
            ->orderBy('height', 'ASC')
            ->get();
    }

    /**
     * Get the group Programme.
     */
    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    /**
     * Get the group Programme Ordered by height
     */
    public function programmesOrderByHeight()
    {
        return $this->hasMany(Programme::class)->orderBy('height', 'ASC');
    }

    public function programmesOrderByHeightAndActive()
    {
        return $this->hasMany(Programme::class)->orderBy('height', 'ASC')->where('is_active', '=', 1);
    }

}
