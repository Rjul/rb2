/*
| Front TALL — JS complémentaire.
| Livewire embarque et démarre Alpine ; on n'importe donc PAS Alpine ici.
| On enregistre seulement nos briques Alpine réutilisables au moment `alpine:init`.
*/
import { player } from './tall/player';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('player', player);
});
