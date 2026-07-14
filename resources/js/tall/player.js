/*
| Lecteur audio persistant (Alpine).
| Vit dans le layout, à l'intérieur de @persist('player') : le nœud DOM (et donc
| l'<audio> + cet état) survit à la navigation wire:navigate → la lecture continue.
|
| Les cartes déclenchent la lecture / la mise en file via des événements DOM :
|   $dispatch('rb:play',  { title, prog, art, src, duration })
|   $dispatch('rb:queue', { title, prog, art, src })
*/
export function player() {
    return {
        current: { title: '', prog: '', art: '' },
        hasTrack: false,
        playing: false,
        progress: 0,
        queue: [],

        init() {
            this.audio = this.$refs.audio;

            window.addEventListener('rb:play', (e) => this.play(e.detail));
            window.addEventListener('rb:queue', (e) => this.enqueue(e.detail));

            this.audio.addEventListener('timeupdate', () => {
                this.progress = this.audio.duration
                    ? (this.audio.currentTime / this.audio.duration) * 100
                    : 0;
            });
            this.audio.addEventListener('play', () => (this.playing = true));
            this.audio.addEventListener('pause', () => (this.playing = false));
            this.audio.addEventListener('ended', () => this.next());
        },

        play(track) {
            this.current = track;
            this.hasTrack = true;
            if (track.src) {
                this.audio.src = track.src;
                this.audio.play().catch(() => {});
            } else {
                // Pas de fichier (démo/placeholder) : on simule juste l'état "en lecture".
                this.playing = true;
            }
        },

        toggle() {
            if (!this.hasTrack) return;
            if (this.audio.src) {
                this.playing ? this.audio.pause() : this.audio.play().catch(() => {});
            } else {
                this.playing = !this.playing;
            }
        },

        enqueue(track) {
            this.queue.push(track);
        },

        next() {
            if (this.queue.length) {
                this.play(this.queue.shift());
            } else {
                this.playing = false;
            }
        },

        seek(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            const ratio = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
            if (this.audio.duration) this.audio.currentTime = ratio * this.audio.duration;
        },

        get currentTime() {
            return this.fmt(this.audio?.currentTime);
        },
        get totalTime() {
            return this.fmt(this.audio?.duration);
        },
        fmt(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return m + ':' + String(s).padStart(2, '0');
        },
    };
}
