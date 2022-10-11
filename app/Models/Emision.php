<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;


class Emision extends Model
{
    use AsSource, Attachable, Filterable;


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
        'title',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'active',
        'active_at'
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
    protected $allowedFilters = [
        'programme_id',
        'user_id',
        'title',
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
        'title',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'active',
        'active_at'
    ];
}
