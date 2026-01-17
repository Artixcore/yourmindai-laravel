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
    
    <!-- Styles -->
    <x-vite-assets />
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-50 to-teal-50">
    <x-navbar />
    
    <main>
        @yield('content')
    </main>
    
    <x-footer />
</body>
</html>
