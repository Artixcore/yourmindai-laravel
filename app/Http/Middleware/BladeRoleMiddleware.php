<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BladeRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = strtolower((string) $user->role);
        $allowedRoles = array_map('strtolower', array_map('strval', $roles));

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Access denied. Required roles: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
