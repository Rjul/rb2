<?php

namespace App\Models;

class Attachment extends \Orchid\Attachment\Models\Attachment
{
    protected $fillable = [
        'name',
        'original_name',
        'mime',
        'extension',
        'size',
        'path',
        'user_id',
        'description',
        'alt',
        'sort',
        'hash',
        'disk',
        'group',
        'duration'
    ];

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }
}
