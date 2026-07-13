<?php

namespace App\Models\Concerns;

use App\Models\Comment;

/**
 * À ajouter au modèle User pour retrouver les commentaires d'un utilisateur.
 * Remplacement natif de Laravelista\Comments\Commenter.
 */
trait Commenter
{
    /**
     * Tous les commentaires postés par cet utilisateur.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commenter');
    }

    /**
     * Uniquement les commentaires approuvés postés par cet utilisateur.
     */
    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commenter')->where('approved', true);
    }
}
