<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center p-5">
        <i class="bi bi-exclamation-circle text-danger" style="font-size: 4rem;"></i>
        <h1 class="display-4 fw-bold mt-3">500</h1>
        <p class="lead text-muted">Server Error</p>
        <p class="text-muted">Something went wrong. Please try again later.</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">
            <i class="bi bi-house me-2"></i>Go to Home
        </a>
    </div>
</body>
</html>
