/*
| Lecteur audio persistant (Alpine).
| Vit dans le layout, à l'intérieur de @persist('player') : le nœud DOM (et donc
| l'<audio> + cet état) survit à wire:navigate → la lecture continue.
|
| Les cartes déclenchent la lecture / la file via des événements window :
|   $dispatch('rb:play',  { title, prog, art, src, duration })   // duration en secondes
|   $dispatch('rb:queue', { title, prog, art, src, duration })
|
| Si le fichier (src) est absent/injouable, on bascule en lecture SIMULÉE
| (barre qui avance + seek cliquable) — utile en dev où les fichiers du seed n'existent pas.
*/
export function player() {
    return {
        current: { title: '', prog: '', art: '', src: null, duration: 0 },
        hasTrack: false,
        playing: false,
        simMode: false,
        progress: 0,
        elapsed: 0,
        duration: 0,
        queue: [],
        open: false,
        _sim: null,
        // Scrub (glisser sur la barre) : position visuelle pendant le drag,
        // le son ne se cale qu'au relâcher (pas de saccades réseau).
        scrubbing: false,
        scrubRatio: 0,

        init() {
            this.audio = this.$refs.audio;

            window.addEventListener('rb:play', (e) => this.play(e.detail));
            window.addEventListener('rb:queue', (e) => this.enqueue(e.detail));

            this.audio.addEventListener('loadedmetadata', () => {
                if (this.audio.duration) this.duration = this.audio.duration;
            });
            this.audio.addEventListener('timeupdate', () => {
                if (this.simMode) return;
                this.elapsed = this.audio.currentTime;
                this.duration = this.audio.duration || this.duration;
                this.progress = this.duration ? (this.elapsed / this.duration) * 100 : 0;
            });
            this.audio.addEventListener('play', () => (this.playing = true));
            this.audio.addEventListener('pause', () => (this.playing = false));
            this.audio.addEventListener('ended', () => this.next());
        },

        play(track) {
            this.stopSim();
            this.current = track;
            this.hasTrack = true;
            this.elapsed = 0;
            this.progress = 0;
            this.duration = track.duration || 0;

            if (track.src) {
                this.audio.src = track.src;
                this.audio.play()
                    .then(() => { this.simMode = false; })
                    .catch(() => this.simulate());
            } else {
                this.simulate();
            }
        },

        // Lecture simulée (pas de fichier) : la barre avance seule.
        simulate() {
            this.simMode = true;
            this.playing = true;
            if (!this.duration) this.duration = 180;
            this.stopSim();
            this._sim = setInterval(() => {
                if (!this.playing) return;
                this.elapsed = Math.min(this.duration, this.elapsed + 1);
                this.progress = (this.elapsed / this.duration) * 100;
                if (this.elapsed >= this.duration) this.next();
            }, 1000);
        },

        stopSim() {
            if (this._sim) { clearInterval(this._sim); this._sim = null; }
        },

        toggle() {
            if (!this.hasTrack) return;
            if (this.simMode) {
                this.playing = !this.playing;
            } else {
                this.audio.paused ? this.audio.play().catch(() => {}) : this.audio.pause();
            }
        },

        /*
        | Scrub à la souris ou au doigt (pointer events + capture) : un simple
        | clic/tap est couvert aussi (press → release au même endroit) — cette
        | mécanique remplace l'ancien seek() au clic. touch-action:none sur la
        | barre empêche le scroll de voler le geste.
        */
        scrubStart(e) {
            if (!this.hasTrack) return;
            // La capture garde le drag fluide même si le doigt sort de la barre ;
            // si elle échoue (pointeur déjà relâché…), le scrub marche quand même.
            try { e.currentTarget.setPointerCapture(e.pointerId); } catch { /* non bloquant */ }
            this.scrubbing = true;
            this._scrubEl = e.currentTarget;
            this.scrubMove(e);
        },

        scrubMove(e) {
            if (!this.scrubbing || !this._scrubEl) return;
            const rect = this._scrubEl.getBoundingClientRect();
            this.scrubRatio = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
        },

        scrubEnd() {
            if (!this.scrubbing) return;
            this.seekTo(this.scrubRatio);
            this.scrubbing = false;
            this._scrubEl = null;
        },

        seekTo(ratio) {
            ratio = Math.min(1, Math.max(0, ratio));
            if (!this.simMode && this.audio.duration) {
                this.audio.currentTime = ratio * this.audio.duration;
            } else {
                this.elapsed = ratio * (this.duration || 180);
                this.progress = ratio * 100;
            }
        },

        // Décalage clavier (flèches sur la barre de progression) : ±10 s.
        nudge(sec) {
            if (!this.hasTrack) return;
            const total = (!this.simMode && this.audio.duration) ? this.audio.duration : (this.duration || 180);
            this.seekTo((this.elapsed + sec) / total);
        },

        enqueue(track) { this.queue.push(track); this.open = true; },
        playFromQueue(i) { const t = this.queue.splice(i, 1)[0]; if (t) this.play(t); },
        removeFromQueue(i) { this.queue.splice(i, 1); },
        clearQueue() { this.queue = []; },

        next() {
            this.stopSim();
            if (this.queue.length) {
                this.play(this.queue.shift());
            } else {
                this.playing = false;
            }
        },

        get currentTime() { return this.fmt(this.elapsed); },
        get totalTime() { return this.fmt(this.duration); },

        // Pendant un scrub, la barre et le temps affichent la CIBLE du doigt ;
        // sinon la position réelle de lecture.
        get displayProgress() { return this.scrubbing ? this.scrubRatio * 100 : this.progress; },
        get displayTime() {
            return this.scrubbing ? this.fmt(this.scrubRatio * (this.duration || 0)) : this.fmt(this.elapsed);
        },
        fmt(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return m + ':' + String(s).padStart(2, '0');
        },
    };
}
