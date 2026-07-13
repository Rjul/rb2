<?php

namespace App\Models\Concerns;

use App\Models\Comment;

/**
 * À ajouter aux modèles qui peuvent recevoir des commentaires.
 * Remplacement natif de Laravelista\Comments\Commentable.
 */
trait Commentable
{
    /**
     * Supprime les commentaires associés lorsque le modèle commenté est supprimé.
     */
    protected static function bootCommentable()
    {
        static::deleted(function ($commentable) {
            foreach ($commentable->comments as $comment) {
                $comment->delete();
            }
        });
    }

    /**
     * Tous les commentaires de ce modèle.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Uniquement les commentaires approuvés.
     */
    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('approved', true);
    }
}
