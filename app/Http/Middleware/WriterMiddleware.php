<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WriterMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        // Check if user can write articles (is_writer OR doctor OR admin)
        if (!$user->canWriteArticles()) {
            abort(403, 'You do not have permission to access the writer panel.');
        }
        
        return $next($request);
    }
}
