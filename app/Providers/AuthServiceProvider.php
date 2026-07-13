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

        // Permissions de commentaires (remplace la config du paquet laravelista/comments)
        Gate::define('create-comment', fn ($user) => true);
        Gate::define('delete-comment', fn ($user, Comment $comment) => $user->getKey() == $comment->commenter_id);
        Gate::define('edit-comment', fn ($user, Comment $comment) => $user->getKey() == $comment->commenter_id);
        Gate::define('reply-to-comment', fn ($user, Comment $comment) => $user->getKey() != $comment->commenter_id);
    }
}
