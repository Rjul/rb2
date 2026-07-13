@php
    $comments = (isset($approved) && $approved === true) ? $model->approvedComments : $model->comments;
    $comments = $comments->sortBy('created_at');
    $grouped_comments = $comments->groupBy('child_id');
@endphp

@if($comments->count() < 1)
    <div class="alert alert-warning">Aucun commentaire pour le moment</div>
@endif

<div>
    @foreach($grouped_comments as $comment_id => $childComments)
        @if($comment_id == '')
            @foreach($childComments as $comment)
                @include('comments._comment', [
                    'comment' => $comment,
                    'grouped_comments' => $grouped_comments,
                    'maxIndentationLevel' => $maxIndentationLevel ?? 3,
                ])
            @endforeach
        @endif
    @endforeach
</div>

@auth
    @include('comments._form')
@else
    <div class="card">
        <div class="card-body d-flex align-items-center">
            <section>
                <h5 class="card-title">Connectez-vous</h5>
                <p class="card-text">Vous devez être connecté pour commenter les articles</p>
            </section>
            <button type="button" style="z-index: 999" class="btn btn-header-login-modal" data-bs-toggle="modal" data-bs-target="#ModalFormLogin">
                <div class="icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="#608762" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                </div>
            </button>
        </div>
    </div>
@endauth
