<?php

namespace App\Models;

use App\Models\Emision as Emission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravelista\Comments\Commentable;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Platform\Dashboard;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;


class Emision extends Model
{
    use HasFactory, HasSlug, Attachable;
    use AsSource, Filterable, HasTags, Commentable;

    const TYPE_TEXT = 'text';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    /**
     * Avalable type audio / video
     * @Todo voir pour le contenue text
     * @param string $type
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getLastByType(string $type = 'audio', int $limite = 4) {
        return self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->where('media_type', '=', $type)
            ->orderBy('active_at', 'desc')
            ->where('active_at', '<', now())
            ->where('active', true)
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
    }

    public static function getLastALaUne(int $limite = 4) {
        return self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->where('is_put_forward', true)
            ->orderBy('emisions.active_at', 'DESC')
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
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
                $builder->orWhere('programme_id', $id);
            };
            return $builder;
        }

        return $builder;
    }

    /**
     * Get the group Programme for the blog post.
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function attachment(string $group = null, ?int $duration = null): MorphToMany
    {
        $query = $this->morphToMany(
            Dashboard::model(\App\Models\Attachment::class),
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }
        if ($duration !== null) {
            $query->where('duration', $duration);
        }

        return $query
            ->orderBy('sort');
    }
    public function attachments(string $group = null, ?int $duration = null): MorphToMany
    {
        $query = $this->morphToMany(
            Dashboard::model(\App\Models\Attachment::class),
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }
        if ($duration !== null) {
            $query->where('duration', $duration);
        }

        return $query
            ->orderBy('sort');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'programme_id',
        'user_id',
        'name',
        'description',
        'duration',
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
    protected $allowedFilters = [
        'programme_id',
        'programme.name',
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
        'active_at',
        'programme.name'
    ];
}
