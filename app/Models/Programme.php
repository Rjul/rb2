<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static public function allActiveEmisions()
    {
        return self::query()->where('is_active', true);
    }

    public function scopeWithAuthPermissions(Builder $builder): Builder
    {
        $programmes_id = [];
        $permisions = Auth()->user()->permissions;
        if (!Auth()->user()->roles->isEmpty()) {
            foreach (Auth()->user()->roles as $role) {
                $permisions = array_merge($permisions ?? [], $role->permissions);
            }
        }
        foreach ($permisions as $permision => $key) {
            if ($permision === "platform.programmes" && ($key === "1" || $key === true) ) {
                return $builder;

            } else if (str_contains($permision, 'platform.emission.') && ($key === "1" || $key === true) ) {
                preg_match('/platform\.emission\.([0-9]+)/', $permision, $matches);
                $programmes_id[] = $matches[1];
            }
        }
        if (!empty($programmes_id)) {
            foreach ($programmes_id as $id) {
                $builder->orWhere('id', $id);
            };
            return $builder;
        }
        $builder->where('id', 999999);

        return $builder;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'height',
        'group_programme_id',
        'user_id',
        'name',
        'description',
        'image',
        'is_active',
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
        'is_active',
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
        'is_active',
        'is_archived',
        'updated_at',
        'created_at',
        'height',
    ];
}
