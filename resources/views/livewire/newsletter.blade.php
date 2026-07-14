<section class="mx-auto max-w-[1200px] px-6 pb-16 pt-6">
    <div class="grid items-center gap-8 rounded-[26px] bg-green-d p-11 text-slate-300 md:grid-cols-[1.2fr_1fr]">
        <div>
            <h3 class="font-display text-[clamp(26px,3.2vw,36px)] text-white">Ne ratez aucune émission.</h3>
            <p class="mt-1">La sélection de la semaine dans votre boîte mail, tous les vendredis. Sans spam, promis.</p>
        </div>

        @if ($done)
            <p class="font-display text-[20px] text-white">Merci ! Votre inscription est bien prise en compte&nbsp;✦</p>
        @else
            <form wire:submit="subscribe" class="flex flex-col gap-2">
                <div class="flex gap-2.5">
                    <input type="email" wire:model="email" placeholder="votre@email.fr" aria-label="Votre adresse email"
                           class="min-w-0 flex-1 rounded-xl border-0 px-4 py-3.5 text-ink">
                    <button type="submit" wire:loading.attr="disabled" wire:target="subscribe"
                            class="rounded-xl bg-green px-6 py-3.5 font-display text-[17px] text-white transition hover:bg-green/80 disabled:opacity-60">
                        <span wire:loading.remove wire:target="subscribe">S'inscrire</span>
                        <span wire:loading wire:target="subscribe">…</span>
                    </button>
                </div>
                @error('email') <span class="text-sm text-red-200">{{ $message }}</span> @enderror
            </form>
        @endif
    </div>
</section>
