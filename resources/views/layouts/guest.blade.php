<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Your Mind Aid - Mental Health Care')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Custom Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gradient-guest">
    <x-navbar />
    
    <main>
        <div class="container-fluid px-3 py-2">
            <x-alerts />
        </div>
        @yield('content')
    </main>
    
    <!-- Background music control (optional, user-controllable) -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;" x-data="backgroundMusic()">
        <div class="d-flex align-items-center gap-2 bg-white rounded-3 shadow-sm border border-stone-200 px-3 py-2">
            <button type="button" class="btn btn-link text-stone-700 p-0 border-0" @click="toggle()" :title="playing ? 'Pause' : 'Play'">
                <i class="bi fs-5" :class="playing ? 'bi-pause-circle-fill' : 'bi-play-circle-fill'"></i>
            </button>
            <div class="d-flex align-items-center gap-1" style="width: 80px;">
                <i class="bi bi-volume-down text-stone-500 small"></i>
                <input type="range" class="form-range form-range-sm" min="0" max="100" x-model="volumePercent" @input="setVolume($event.target.value)">
            </div>
        </div>
        <audio id="bg-music" loop preload="metadata" src="{{ asset('audio/background.mp3') }}" x-ref="audio"></audio>
    </div>
    
    <x-footer />
    
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- jQuery (for AJAX form handling) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app-ajax.js') }}"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    
    <!-- Alpine.js Components -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modal', (initialOpen = false) => ({
                open: initialOpen,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                },
            }));

            Alpine.data('dropdown', (initialOpen = false) => ({
                open: initialOpen,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                },
            }));

            Alpine.data('sidebar', (initialOpen = false) => ({
                open: initialOpen,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                },
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
        
        // Initialize AOS
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 800,
                        easing: 'ease-in-out',
                        once: true,
                        offset: 100,
                    });
                }
            });
        } else {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100,
                });
            }
        }
    </script>
</body>
</html>
