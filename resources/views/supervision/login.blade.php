<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supervision Login - Your Mind Aid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-container { max-width: 450px; width: 100%; }
        .login-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .login-header { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); padding: 40px 30px; text-align: center; color: white; }
        .login-header h1 { font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .login-body { padding: 40px 30px; }
        .form-control { border: 2px solid #e5e7eb; border-radius: 10px; padding: 12px 16px; }
        .btn-login { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); border: none; border-radius: 10px; padding: 14px; font-weight: 600; color: white; width: 100%; }
        .btn-login:hover { color: white; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="mb-3"><i class="bi bi-shield-check" style="font-size: 3rem;"></i></div>
                <h1>Supervision Portal</h1>
                <p class="mb-0">Sign in to verify tasks and add remarks</p>
            </div>
            <div class="login-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('email') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('supervision.login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter username or email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
                <div class="text-center mt-3 pt-3 border-top">
                    <a href="{{ route('parent.login') }}" class="text-decoration-none small">Parent Login</a>
                    <span class="mx-2">|</span>
                    <a href="{{ route('login') }}" class="text-decoration-none small">Staff Login</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
