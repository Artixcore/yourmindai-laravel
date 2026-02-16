<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Return a standardized API error JSON response.
     * Contract: success=false, message, errors (array), code.
     */
    public static function error(string $message, int $code = 500, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
        ], $code >= 100 && $code < 600 ? $code : 500);
    }
}
