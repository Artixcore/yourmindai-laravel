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
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Admin Panel Styles (loads after Bootstrap, before app.css) -->
    <link href="https://storaeall.s3.us-east-1.amazonaws.com/public/admin.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="https://storaeall.s3.us-east-1.amazonaws.com/public/app.css" rel="stylesheet">
    <link href="https://storaeall.s3.us-east-1.amazonaws.com/public/patient.css" rel="stylesheet">
    
    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="bg-stone-50">
    <div class="d-flex" style="height: 100vh; overflow: hidden;">
        <!-- Sidebar -->
        <div class="d-none d-md-block">
            <x-sidebar :role="auth()->user()->role ?? 'assistant'" />
        </div>
        
        <!-- Mobile sidebar (Bootstrap Offcanvas) -->
        <div class="offcanvas offcanvas-start d-md-none"
             tabindex="-1"
             id="mobileSidebar"
             aria-labelledby="mobileSidebarLabel"
             style="z-index: 1040; --bs-offcanvas-width: 256px;">

            <div class="offcanvas-header border-bottom border-stone-200 d-flex justify-content-end py-2">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0 overflow-y-auto">
                <x-sidebar :role="auth()->user()->role ?? 'assistant'" />
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex-grow-1 d-flex flex-column overflow-hidden sidebar-margin">
            <x-topbar :user="auth()->user()" />
            
            <main class="flex-grow-1 overflow-y-auto" style="padding-top: 64px;">
                <div class="p-4 p-md-5">
                    <x-alerts />
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>window.Bootstrap = window.bootstrap;</script>

    <!-- jQuery (for AJAX form handling) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://storaeall.s3.us-east-1.amazonaws.com/public/js/app-ajax.js"></script>
    <script src="https://storaeall.s3.us-east-1.amazonaws.com/public/js/notifications.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    
    <!-- Alpine.js Components -->
    <script>
        // Initialize sidebar collapsed state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            document.body.setAttribute('data-sidebar-collapsed', collapsed);
        });

        // Register Alpine components - this will run before Alpine processes the page
        // because Alpine.js is loaded with 'defer' attribute
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
        });

        // Fallback: If Alpine is already loaded, register components immediately
        if (window.Alpine) {
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
        }
        
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
        
        // Session flash messages and validation errors are displayed via <x-alerts /> (Bootstrap)
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        /* Sidebar inside offcanvas: override position-fixed so it flows with content */
        #mobileSidebar .sidebar-width.position-fixed {
            position: static !important;
            height: auto !important;
        }
        /* Skeleton loader for heavy sections */
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 4px; }
        @keyframes skeleton-loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
    
    @stack('scripts')
</body>
</html>
