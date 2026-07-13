<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Modèle de commentaire natif (remplace Laravelista\Comments\Comment).
 * Commentaires polymorphes sur les émissions, réponses imbriquées, modération.
 */
class Comment extends Model
{
    use HasFactory, Filterable, SortableTrait, SoftDeletes;

    /**
     * Relations chargées à chaque requête.
     */
    protected $with = ['commenter'];

    protected $fillable = [
        'comment', 'approved', 'guest_name', 'guest_email',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    protected $allowedFilters = [
        'comment'     => Like::class,
        'approved'    => Where::class,
        'guest_name'  => Like::class,
        'guest_email' => Like::class,
    ];

    /**
     * Par défaut un commentaire est en attente de modération.
     */
    public function __construct(array $attributes = [])
    {
        if (!array_key_exists('approved', $attributes)) {
            $attributes['approved'] = false;
        }

        parent::__construct($attributes);
    }

    /**
     * L'auteur du commentaire (utilisateur).
     */
    public function commenter()
    {
        return $this->morphTo();
    }

    /**
     * Le modèle commenté (une émission).
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Les réponses à ce commentaire.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'child_id');
    }

    /**
     * Le commentaire parent.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'child_id');
    }
}
