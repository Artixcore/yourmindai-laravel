<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Client Portal - Your Mind Aid')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #f8f9fa;
            padding-bottom: 80px; /* Space for bottom nav */
        }
        
        .client-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .client-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 0.5rem 0;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .nav-item.active {
            color: #667eea;
        }
        
        .nav-item i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .nav-item span {
            font-size: 0.75rem;
            display: block;
        }
        
        .content-wrapper {
            padding: 1rem;
            max-width: 100%;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }
        
        .stats-card .number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .stats-card .label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <div class="client-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold">Your Mind Aid</h5>
                <small class="opacity-75">{{ auth()->user()->name ?? 'Patient' }}</small>
            </div>
            <form method="POST" action="{{ route('client.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Bottom Navigation -->
    <nav class="client-nav d-flex">
        <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('client.assessments.index') }}" class="nav-item {{ request()->routeIs('client.assessments.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i>
            <span>Assessments</span>
        </a>
        <a href="{{ route('client.tasks.index') }}" class="nav-item {{ request()->routeIs('client.tasks.*') ? 'active' : '' }}">
            <i class="bi bi-list-check"></i>
            <span>Tasks</span>
        </a>
        <a href="{{ route('client.devices.index') }}" class="nav-item {{ request()->routeIs('client.devices.*') ? 'active' : '' }}">
            <i class="bi bi-phone"></i>
            <span>Devices</span>
        </a>
        <a href="{{ route('client.contingency.index') }}" class="nav-item {{ request()->routeIs('client.contingency.*') ? 'active' : '' }}">
            <i class="bi bi-shield-exclamation"></i>
            <span>Emergency</span>
        </a>
    </nav>
    
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @stack('scripts')
</body>
</html>
