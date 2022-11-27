<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravelista\Comments\Commentable;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Spatie\Translatable\HasTranslations;

class Programme extends Model
{
    use HasFactory, Commentable, HasSlug;
    use AsSource, Attachable, Filterable;
    /**
     * Get the group Programme for the blog post.
     */
    public function group_programme()
    {
        return $this->belongsTo(GroupProgramme::class);
    }
    /**
     * Get the group Programme for the blog post.
     */
    public function emisions()
    {
        return $this->hasMany(Emision::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'height',
        'group_programme_id',
        'user_id',
        'name',
        'description',
        'image',
        'active',
        'is_archived'
    ];


    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'id'])
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
    protected array $allowedFilters = [
        'height',
        'group_programme_id',
        'user_id',
        'name',
        'description',
        'image',
        'active',
        'is_archived'
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected array $allowedSorts = [
        'id',
        'group_programme_id',
        'user_id',
        'name',
        'active',
        'is_archived',
        'updated_at',
        'created_at',
        'height',
    ];
}
