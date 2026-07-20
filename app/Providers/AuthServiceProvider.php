<?php

namespace App\Providers;

use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Permissions de commentaires (remplace la config du paquet laravelista/comments).
        // Écrire un commentaire exige un email VÉRIFIÉ (anti-bot) ; single source of
        // vérité → couvre à la fois le Livewire CommentThread et WebCommentController.
        Gate::define('create-comment', fn ($user) => $user->hasVerifiedEmail());
        Gate::define('delete-comment', fn ($user, Comment $comment) => $user->getKey() == $comment->commenter_id);
        Gate::define('edit-comment', fn ($user, Comment $comment) => $user->getKey() == $comment->commenter_id && $user->hasVerifiedEmail());
        Gate::define('reply-to-comment', fn ($user, Comment $comment) => $user->getKey() != $comment->commenter_id && $user->hasVerifiedEmail());
    }
}
