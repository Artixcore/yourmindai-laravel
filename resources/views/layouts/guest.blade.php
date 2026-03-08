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
    
    <!-- All CSS/JS from local build (Vite → public/build) -->
    @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/js/guest.js'])
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
    
    <!-- Local scripts (public folder) -->
    <script src="{{ asset('js/app-ajax.js') }}" defer></script>
    <script src="{{ asset('js/notifications.js') }}" defer></script>
    
    <!-- Flash messages (SweetAlert from guest.js bundle) -->
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
