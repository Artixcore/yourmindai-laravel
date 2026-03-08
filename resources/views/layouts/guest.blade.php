<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Your Mind Aid - Mental Health Care')</title>
    <meta name="description" content="@yield('meta_description', 'Compassionate mental health care for your journey to wellness. Book an appointment with our expert team at Your Mind Aid.')">
    <link rel="icon" href="{{ asset('mindaidlogo.png') }}" type="image/x-icon">
    
    @stack('meta')
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Your Mind Aid - Mental Health Care')">
    <meta property="og:description" content="@yield('og_description', 'Compassionate mental health care for your journey to wellness. Book an appointment with our expert team.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @else
        <meta property="og:image" content="{{ asset('images/og-default.png') }}">
    @endif
    
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/js/guest.js'])
    @else
        {{-- Fallback when Vite manifest missing (e.g. production without npm run build) --}}
        <link href="{{ asset('app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/guest.css') }}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @endif
</head>
<body class="bg-gradient-guest d-flex flex-column min-vh-100">
    <x-navbar />
    <main class="main-guest flex-grow-1">
        <div class="container-fluid px-3 py-2">
            <x-alerts />
        </div>
        @yield('content')
    </main>
    
    <!-- Background music control (optional, user-controllable) -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;" x-data="backgroundMusic()" role="group" aria-label="Background music">
        <div class="d-flex align-items-center gap-2 bg-white rounded-3 shadow-sm border border-stone-200 px-3 py-2">
            <button type="button" class="btn btn-link text-stone-700 p-0 border-0" @click="toggle()" :aria-label="playing ? 'Pause background music' : 'Play background music'" :title="playing ? 'Pause' : 'Play'">
                <i class="bi fs-5" :class="playing ? 'bi-pause-circle-fill' : 'bi-play-circle-fill'" aria-hidden="true"></i>
            </button>
            <div class="d-flex align-items-center gap-1" style="width: 80px;">
                <label for="bg-music-volume" class="visually-hidden">Volume</label>
                <input id="bg-music-volume" type="range" class="form-range form-range-sm" min="0" max="100" x-model="volumePercent" @input="setVolume($event.target.value)" aria-label="Volume">
            </div>
        </div>
        <audio id="bg-music" loop preload="metadata" src="{{ asset('audio/background.mp3') }}" x-ref="audio"></audio>
    </div>
    
    <x-footer />
    
    @if(!file_exists(public_path('build/manifest.json')))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>window.axios = axios; window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';</script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modal', (o = false) => ({ open: o, toggle() { this.open = !this.open; }, close() { this.open = false; } }));
            Alpine.data('dropdown', (o = false) => ({ open: o, toggle() { this.open = !this.open; }, close() { this.open = false; } }));
            Alpine.data('sidebar', (o = false) => ({ open: o, toggle() { this.open = !this.open; }, close() { this.open = false; } }));
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
                        if (!prefs.muted) audio.play().then(() => { this.playing = true; }).catch(() => {});
                        audio.addEventListener('play', () => { this.playing = true; });
                        audio.addEventListener('pause', () => { this.playing = false; });
                    },
                    toggle() {
                        const audio = this.$refs.audio;
                        if (!audio) return;
                        if (this.playing) { audio.pause(); prefs.muted = true; }
                        else { audio.volume = this.volume; audio.play().then(() => { prefs.muted = false; }).catch(() => {}); }
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
        window.addEventListener('load', () => { if (typeof AOS !== 'undefined') AOS.init({ duration: 800, easing: 'ease-in-out', once: true, offset: 100 }); });
        </script>
    @endif
    <script src="{{ asset('js/app-ajax.js') }}" defer></script>
    <script src="{{ asset('js/notifications.js') }}" defer></script>
    <!-- Flash messages -->
    <script>
        (function() {
            const error = @json(session('error'));
            const warning = @json(session('warning'));
            const info = @json(session('info'));
            if (error && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: error, confirmButtonColor: '#0d6efd' });
            }
            if (warning && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Notice', text: warning, confirmButtonColor: '#0d6efd' });
            }
            if (info && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'Info', text: info, confirmButtonColor: '#0d6efd' });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
