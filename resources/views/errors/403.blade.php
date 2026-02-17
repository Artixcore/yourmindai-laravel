<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center p-5">
        <i class="bi bi-shield-exclamation text-danger" style="font-size: 4rem;"></i>
        <h1 class="display-4 fw-bold mt-3">403</h1>
        <p class="lead text-muted">Access Denied</p>
        <p class="text-muted">You do not have permission to access this page.</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go to Home</a>
    </div>
</body>
</html>
