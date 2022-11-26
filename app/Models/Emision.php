<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;


class Emision extends Model
{
    use HasFactory, HasSlug;
    use AsSource, Attachable, Filterable, HasTags;

    /**
     * Avalable type audio / video
     * @Todo voir pour le contenue text
     * @param string $type
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getLastByType(string $type = 'audio', int $limite = 4) {
        return self::query()
            ->where('media_type', '=', $type)
            ->leftJoin('programmes', 'emisions.programme_id', '=', 'programmes.id')
            ->orderBy('emisions.active_at', 'DESC')
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
    }

    /**
     * Get the group Programme for the blog post.
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'programme_id',
        'user_id',
        'name',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'active',
        'active_at'
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
        'programme_id',
        'user_id',
        'name',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'active',
        'active_at'
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'programme_id',
        'user_id',
        'name',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'active',
        'active_at'
    ];
}
