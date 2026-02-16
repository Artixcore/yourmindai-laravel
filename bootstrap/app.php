<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JwtAuth::class,
            'role' => \App\Http\Middleware\RequireRole::class,
            'blade.role' => \App\Http\Middleware\BladeRoleMiddleware::class,
            'require.admin' => \App\Http\Middleware\RequireAdmin::class,
            'writer' => \App\Http\Middleware\WriterMiddleware::class,
        ]);
        
        // Enable CORS for API routes
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            ValidationException::class,
        ]);

        $exceptions->context(fn () => [
            'user_id' => auth()->id(),
            'route' => request()->route()?->getName() ?? request()->path(),
            'request' => sanitizeRequest(request()),
        ]);

        // ModelNotFoundException -> 404 (web: redirect + flash; API: JSON)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Record not found.'], 404);
            }
            return redirect()->back()->with('error', 'The requested resource was not found.');
        });

        // NotFoundHttpException -> 404 for API
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $e->getMessage() ?: 'Not found.'], 404);
            }
        });

        // AuthorizationException -> 403
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $e->getMessage() ?: 'This action is unauthorized.'], 403);
            }
            return redirect()->back()->with('error', $e->getMessage() ?: 'You are not authorized to perform this action.');
        });

        // General API exception handler (from origin/main)
        $exceptions->render(function (Throwable $e, Request $request) {
            $isApi = $request->expectsJson() || $request->is('api/*');

            if (!$isApi) {
                return null;
            }

            $debug = config('app.debug');
            $code = 500;
            $message = 'Something went wrong.';
            $errors = [];

            if ($e instanceof ValidationException) {
                $code = 422;
                $message = $e->getMessage() ?: 'The given data was invalid.';
                $errors = $e->errors();
            } elseif ($e instanceof AuthenticationException) {
                $code = 401;
                $message = 'Unauthenticated.';
            } elseif ($e instanceof AuthorizationException) {
                $code = 403;
                $message = $e->getMessage() ?: 'This action is unauthorized.';
            } elseif ($e instanceof ModelNotFoundException) {
                $code = 404;
                $message = 'The requested resource was not found.';
            } elseif ($e instanceof QueryException) {
                $code = 500;
                $message = 'A database error occurred.';
                if ($debug) {
                    $message = $e->getMessage();
                }
            } elseif ($e instanceof HttpException) {
                $code = $e->getStatusCode();
                $msg = $e->getMessage();
                $message = ($msg && !str_contains(strtolower($msg), 'exception') && !str_contains($msg, 'sql')) ? $msg : 'An error occurred.';
            } else {
                if ($debug) {
                    $message = $e->getMessage();
                }
            }

            if ($code >= 500) {
                Log::error($e->getMessage(), [
                    'exception' => get_class($e),
                    'user_id' => auth()->id(),
                    'route' => $request->route()?->getName() ?? $request->path(),
                    'request' => sanitizeRequest($request),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'code' => $code,
            ], $code);
        });
    })->create();

/**
 * Sanitize request data for logging (strip secrets).
 */
function sanitizeRequest(Request $request): array
{
    $keys = ['password', 'password_hash', 'password_confirmation', 'token', 'api_token', '_token', 'secret'];
    $all = $request->except($keys);
    foreach ($keys as $key) {
        if ($request->has($key)) {
            $all[$key] = '[REDACTED]';
        }
    }
    return $all;
}
