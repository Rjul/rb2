@php
    $author   = $comment->commenter?->name ?? $comment->guest_name ?? 'Anonyme';
    $initial  = mb_strtoupper(mb_substr($author, 0, 1));
    $children = $thread[$comment->id] ?? collect();
    $canReply = auth()->check() && \Illuminate\Support\Facades\Gate::allows('reply-to-comment', $comment);
    $canEdit  = auth()->check() && \Illuminate\Support\Facades\Gate::allows('edit-comment', $comment);
    $canDel   = auth()->check() && \Illuminate\Support\Facades\Gate::allows('delete-comment', $comment);
@endphp

<div id="comment-{{ $comment->id }}" @class(['border-l-2 border-line pl-4' => $depth > 0])>
    <div class="flex gap-3.5">
        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-green/10 font-display text-[17px] text-green">{{ $initial }}</span>
        <div class="min-w-0 flex-1">
            <div class="flex items-baseline gap-2">
                <span class="font-display text-[17px] text-navy">{{ $author }}</span>
                <span class="text-xs text-muted">{{ $comment->created_at?->diffForHumans() }}</span>
            </div>

            @if ($editing === $comment->id)
                {{-- Édition en place --}}
                <form wire:submit="saveEdit" class="mt-2">
                    <textarea wire:model="editBody" rows="3"
                              class="w-full resize-y rounded-2xl border border-line bg-white px-4 py-2.5 text-[15.5px] outline-none focus:border-green-l"></textarea>
                    @error('editBody') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <div class="mt-2 flex gap-2">
                        <button type="submit" class="rounded-lg bg-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-d">Enregistrer</button>
                        <button type="button" wire:click="$set('editing', null)" class="rounded-lg border border-line px-4 py-2 text-sm text-muted hover:text-navy">Annuler</button>
                    </div>
                </form>
            @else
                <p class="mt-1 text-[15.5px] leading-relaxed text-ink">{!! nl2br(e($comment->comment)) !!}</p>

                <div class="mt-2 flex flex-wrap gap-4 text-[13px] font-semibold text-muted">
                    @if ($canReply)
                        <button type="button" wire:click="startReply({{ $comment->id }})" class="transition hover:text-green">Répondre</button>
                    @endif
                    @if ($canEdit)
                        <button type="button" wire:click="startEdit({{ $comment->id }})" class="transition hover:text-green">Modifier</button>
                    @endif
                    @if ($canDel)
                        <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Supprimer ce commentaire ?" class="transition hover:text-red-600">Supprimer</button>
                    @endif
                </div>
            @endif

            {{-- Formulaire de réponse --}}
            @if ($replyTo === $comment->id)
                <form wire:submit="submitReply" class="mt-3">
                    <textarea wire:model="replyBody" rows="2" placeholder="Votre réponse…"
                              class="w-full resize-y rounded-2xl border border-line bg-white px-4 py-2.5 text-[15.5px] outline-none focus:border-green-l"></textarea>
                    @error('replyBody') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <div class="mt-2 flex gap-2">
                        <button type="submit" class="rounded-lg bg-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-d">Répondre</button>
                        <button type="button" wire:click="$set('replyTo', null)" class="rounded-lg border border-line px-4 py-2 text-sm text-muted hover:text-navy">Annuler</button>
                    </div>
                </form>
            @endif

            {{-- Réponses (récursion, indentation plafonnée à 3 niveaux) --}}
            @if ($children->isNotEmpty())
                <div class="mt-4 flex flex-col gap-4">
                    @foreach ($children as $child)
                        @include('livewire.v2._comment', ['comment' => $child, 'thread' => $thread, 'depth' => min($depth + 1, 3)])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
