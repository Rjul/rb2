<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

/**
 * Gestion des commentaires côté public (remplace Laravelista\Comments\WebCommentController).
 * Réservé aux utilisateurs connectés (voir middleware auth sur les routes).
 * Les nouveaux commentaires sont créés en attente de modération.
 */
class WebCommentController extends Controller
{
    /**
     * Crée un nouveau commentaire pour le modèle donné.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-comment', Comment::class);

        Validator::make($request->all(), [
            'commentable_type' => 'required|string',
            'commentable_id'   => 'required|string|min:1',
            'message'          => 'required|string',
        ])->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);

        $comment = new Comment();
        $comment->commenter()->associate(Auth::user());
        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = false; // modération
        $comment->save();

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }

    /**
     * Met à jour le message d'un commentaire.
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
        ])->validate();

        $comment->update(['comment' => $request->message]);

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }

    /**
     * Supprime un commentaire.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete-comment', $comment);

        $comment->delete();

        return Redirect::back();
    }

    /**
     * Crée une réponse à un commentaire.
     */
    public function reply(Request $request, Comment $comment)
    {
        Gate::authorize('reply-to-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
        ])->validate();

        $reply = new Comment();
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = false; // modération
        $reply->save();

        return Redirect::to(URL::previous() . '#comment-' . $reply->getKey());
    }
}
