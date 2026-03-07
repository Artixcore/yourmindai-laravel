<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Your Mind Aid - Mental Health Care')</title>
    <meta name="description" content="@yield('meta_description', 'Compassionate mental health care for your journey to wellness. Book an appointment with our expert team at Your Mind Aid.')">
    <link rel="icon" href="https://storaeall.s3.us-east-1.amazonaws.com/public/mindaidlogo.png" type="image/x-icon">
    
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
    
    <!-- Preconnect to key origins -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://storaeall.s3.us-east-1.amazonaws.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Custom Styles -->
    <link href="https://storaeall.s3.us-east-1.amazonaws.com/public/css/app.css" rel="stylesheet">
    <link href="{{ asset('css/guest.css') }}" rel="stylesheet">
    
    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        <audio id="bg-music" loop preload="metadata" src="https://storaeall.s3.us-east-1.amazonaws.com/public/audio/background.mp3" x-ref="audio"></audio>
    </div>
    
    <x-footer />
    
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- jQuery (for AJAX form handling) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://storaeall.s3.us-east-1.amazonaws.com/public/js/app-ajax.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- AOS Animation Library (deferred, non-critical) -->
    <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    <!-- SweetAlert2 JS (used for flash messages; must load before inline script) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        
        // Initialize AOS (runs after deferred AOS script loads)
        window.addEventListener('load', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100,
                });
            }
        });

        // Flash messages as SweetAlert (error, warning, info). Success uses inline alerts on guest forms.
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
