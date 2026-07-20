<section class="mx-auto max-w-[1200px] px-6 pb-16 pt-6">
    <div class="grid grid-cols-1 items-center gap-8 rounded-[26px] bg-green-d p-7 text-slate-300 sm:p-11 md:grid-cols-[1.2fr_1fr]">
        <div>
            <h3 class="font-display text-[clamp(26px,3.2vw,36px)] text-white">Ne ratez aucune émission.</h3>
            <p class="mt-1">La sélection de la semaine dans votre boîte mail, tous les vendredis. Sans spam, promis.</p>
        </div>

        @if ($done)
            <p class="font-display text-[20px] text-white">Presque terminé&nbsp;! Vérifiez votre boîte mail et cliquez sur le lien de confirmation&nbsp;✦</p>
        @else
            <form wire:submit="subscribe" class="flex flex-col gap-2">
                {{-- Anti-bot (honeypot) : hors écran, rempli seulement par les bots. --}}
                <div aria-hidden="true" style="position:absolute;left:-9999px;height:0;width:0;overflow:hidden;" tabindex="-1">
                    <label for="nl-website">Laissez ce champ vide</label>
                    <input id="nl-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="flex flex-col gap-2.5 sm:flex-row">
                    <input type="email" wire:model="email" placeholder="votre@email.fr" aria-label="Votre adresse email"
                           class="min-w-0 flex-1 rounded-xl border-0 bg-white px-4 py-3.5 text-ink placeholder:text-ink/55">
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
