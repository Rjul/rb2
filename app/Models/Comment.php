<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Spatie\EloquentSortable\SortableTrait;

class Comment extends \Laravelista\Comments\Comment
{
    use HasFactory, Filterable, SortableTrait;

    protected $allowedFilters = [
        'comment', 'approved', 'guest_name', 'guest_email'
    ];

    public function __construct(array $attributes = [])
    {
        if (!array_key_exists('approved', $attributes)) {
            $attributes['approved'] = false;
        }
        parent::__construct($attributes);
    }
}
