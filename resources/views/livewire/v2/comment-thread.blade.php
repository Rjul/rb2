<section>
    <div class="flex items-baseline justify-between border-t border-line pt-8">
        <h2 class="font-display text-[24px] text-navy">Commentaires</h2>
        <span class="text-sm text-muted">{{ $count }} commentaire{{ $count > 1 ? 's' : '' }}</span>
    </div>

    {{-- Formulaire (utilisateurs connectés) --}}
    @auth
        @if ($submitted)
            <div class="mt-5 rounded-2xl border border-green-l/40 bg-green-l/10 px-5 py-4 text-sm text-green-d">
                Merci ! Votre commentaire a été envoyé et sera publié après modération.
            </div>
        @endif

        <form wire:submit="addComment" class="mt-5">
            <textarea wire:model="body" rows="3" placeholder="Partagez votre avis…"
                      class="w-full resize-y rounded-2xl border border-line bg-white px-4 py-3 text-[16px] text-ink outline-none transition focus:border-green-l"></textarea>
            @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="mt-3 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-green px-6 py-3 font-display text-[17px] text-white transition hover:bg-green-d"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="addComment">Publier</span>
                    <span wire:loading wire:target="addComment">Envoi…</span>
                </button>
            </div>
        </form>
    @else
        <div class="mt-5 rounded-2xl border border-line bg-white px-5 py-4 text-[15px] text-muted">
            <a href="{{ route('login') }}" class="font-semibold text-green hover:text-green-d">Connectez-vous</a>
            pour laisser un commentaire.
        </div>
    @endauth

    {{-- Liste --}}
    @php $roots = $thread[0] ?? collect(); @endphp
    <div class="mt-8 flex flex-col gap-6">
        @forelse ($roots as $comment)
            @include('livewire.v2._comment', ['comment' => $comment, 'thread' => $thread, 'depth' => 0])
        @empty
            <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à réagir&nbsp;!</p>
        @endforelse
    </div>
</section>
