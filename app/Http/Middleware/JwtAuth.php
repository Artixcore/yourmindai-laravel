<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please sign in to continue.',
                ], 401);
            }

            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please sign in to continue.',
                ], 401);
            }

            // Attach user to request
            $request->merge(['user' => $user]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Your session has expired. Please sign in again.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Please sign in to continue.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Please sign in to continue.',
            ], 401);
        }

        return $next($request);
    }
}
