/**
 * Guest (public) layout: Bootstrap, jQuery, Alpine, AOS, Axios, SweetAlert2.
 * All assets loaded from local build – no S3 or CDN.
 */
import 'bootstrap';
import $ from 'jquery';
import axios from 'axios';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';

window.$ = window.jQuery = $;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Swal = Swal;
window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('modal', (initialOpen = false) => ({
        open: initialOpen,
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
    }));
    Alpine.data('dropdown', (initialOpen = false) => ({
        open: initialOpen,
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
    }));
    Alpine.data('sidebar', (initialOpen = false) => ({
        open: initialOpen,
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
    }));
    Alpine.data('backgroundMusic', () => {
        const stored = localStorage.getItem('bgMusic');
        const prefs = stored ? JSON.parse(stored) : { volume: 0.4, muted: true };
        return {
            playing: false,
            volumePercent: Math.round((prefs.volume ?? 0.4) * 100),
            get volume() { return this.volumePercent / 100; },
            init() {
                const audio = this.$refs.audio;
                if (!audio) return;
                audio.volume = this.volume;
                if (!prefs.muted) {
                    audio.play().then(() => { this.playing = true; }).catch(() => {});
                }
                audio.addEventListener('play', () => { this.playing = true; });
                audio.addEventListener('pause', () => { this.playing = false; });
            },
            toggle() {
                const audio = this.$refs.audio;
                if (!audio) return;
                if (this.playing) {
                    audio.pause();
                    prefs.muted = true;
                } else {
                    audio.volume = this.volume;
                    audio.play().then(() => { prefs.muted = false; }).catch(() => {});
                }
                localStorage.setItem('bgMusic', JSON.stringify({ volume: this.volume, muted: prefs.muted }));
            },
            setVolume(val) {
                const v = parseInt(val, 10) / 100;
                if (this.$refs.audio) this.$refs.audio.volume = v;
                localStorage.setItem('bgMusic', JSON.stringify({ volume: v, muted: !this.playing }));
            },
        };
    });
});

Alpine.start();

window.addEventListener('load', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, easing: 'ease-in-out', once: true, offset: 100 });
    }
});
