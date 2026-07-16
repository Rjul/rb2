<?php

namespace App\Livewire\V2;

use App\Models\Comment;
use App\Models\Emision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Fil de commentaires d'une émission (front v2).
 * Sécurité : le commentable est FIXÉ côté serveur (l'émission injectée),
 * jamais lu depuis la requête (corrige la faille commentable_type du legacy).
 * Modération conservée : tout nouveau commentaire est approved=false.
 * Autorisations : Gates create/edit/delete/reply-to-comment existantes.
 */
class CommentThread extends Component
{
    public Emision $emission;

    public string $body = '';          // nouveau commentaire racine
    public ?int $replyTo = null;       // id du commentaire auquel on répond
    public string $replyBody = '';
    public ?int $editing = null;       // id du commentaire en édition
    public string $editBody = '';
    public bool $submitted = false;    // feedback « en attente de modération »

    public function mount(Emision $emission): void
    {
        $this->emission = $emission;
    }

    public function addComment(): void
    {
        Gate::authorize('create-comment');
        $this->validate(['body' => 'required|string|min:2|max:5000']);

        $this->persist($this->body, null);

        $this->reset('body');
        $this->submitted = true;
    }

    public function startReply(int $id): void
    {
        $this->reset('editing', 'editBody');
        $this->replyTo = $id;
        $this->replyBody = '';
    }

    public function submitReply(): void
    {
        $parent = Comment::findOrFail($this->replyTo);
        Gate::authorize('reply-to-comment', $parent);
        $this->validate(['replyBody' => 'required|string|min:2|max:5000']);

        $this->persist($this->replyBody, $parent->id);

        $this->reset('replyTo', 'replyBody');
        $this->submitted = true;
    }

    public function startEdit(int $id): void
    {
        $comment = Comment::findOrFail($id);
        Gate::authorize('edit-comment', $comment);
        $this->reset('replyTo', 'replyBody');
        $this->editing = $id;
        $this->editBody = $comment->comment;
    }

    public function saveEdit(): void
    {
        $comment = Comment::findOrFail($this->editing);
        Gate::authorize('edit-comment', $comment);
        $this->validate(['editBody' => 'required|string|min:2|max:5000']);

        // Toute édition repasse en modération (sinon un commentaire approuvé peut être
        // altéré en spam et rester public sans relecture).
        $comment->update(['comment' => $this->editBody, 'approved' => false]);
        $this->reset('editing', 'editBody');
        $this->submitted = true;
    }

    public function deleteComment(int $id): void
    {
        $comment = Comment::findOrFail($id);
        Gate::authorize('delete-comment', $comment);
        $comment->delete();
    }

    /** Crée un commentaire en modération, rattaché à l'émission courante. */
    protected function persist(string $text, ?int $parentId): void
    {
        $comment = new Comment(['comment' => $text, 'approved' => false]);
        $comment->commenter()->associate(Auth::user());
        $comment->commentable()->associate($this->emission);
        if ($parentId) {
            $comment->child_id = $parentId;
        }
        $comment->save();
    }

    public function render()
    {
        // Commentaires approuvés (modération) indexés par parent : 0 = racines.
        $approved = $this->emission->approvedComments()
            ->with('commenter')
            ->orderBy('created_at')
            ->get();

        return view('livewire.v2.comment-thread', [
            'thread' => $approved->groupBy(fn (Comment $c) => $c->child_id ?: 0),
            'count'  => $approved->count(),
        ]);
    }
}
