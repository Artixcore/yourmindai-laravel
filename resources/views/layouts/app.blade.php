<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard - Your Mind Aid')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Custom Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
       
       <link href="{{ asset('https://storaeall.s3.us-east-1.amazonaws.com/public/build/assets/app-Dt_pb0sm.css') }}" rel="stylesheet">
       <link href="{{ asset('https://storaeall.s3.us-east-1.amazonaws.com/public/build/assets/app-DvB2Xm2x.css') }}" rel="stylesheet">
       <script src="{{ asset('https://storaeall.s3.us-east-1.amazonaws.com/public/build/assets/app-DAh8zfi3.js') }}"></script>

   <script src="{{ asset('js/app.js') }}"></script>
   <script src="{{ asset('https://storaeall.s3.us-east-1.amazonaws.com/doctor/assets/app-DAh8zfi3.js') }}"></script>
   
   <!-- Bootstrap 5.3 JS Bundle -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-stone-50">
    <div x-data="{ sidebarOpen: false }" class="d-flex" style="height: 100vh; overflow: hidden;">
        <!-- Sidebar -->
        <div class="d-none d-md-block">
            <x-sidebar :role="auth()->user()->role ?? 'assistant'" />
        </div>
        
        <!-- Mobile sidebar overlay -->
        <div 
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="position-fixed top-0 start-0 w-100 h-100 bg-dark"
            style="display: none; opacity: 0.5; z-index: 1030;"
        ></div>
        
        <!-- Mobile sidebar -->
        <div 
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="position-fixed start-0 top-0 h-100 d-md-none"
            style="display: none; z-index: 1040;"
        >
            <x-sidebar :role="auth()->user()->role ?? 'assistant'" />
        </div>
        
        <!-- Main content -->
        <div class="flex-grow-1 d-flex flex-column overflow-hidden sidebar-margin">
            <x-topbar :user="auth()->user()" />
            
            <main class="flex-grow-1 overflow-y-auto" style="padding-top: 64px;">
                <div class="p-4 p-md-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
