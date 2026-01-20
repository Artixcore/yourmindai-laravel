<?php

namespace App\Http\Controllers;

use App\Models\ClientIntake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientIntakeController extends Controller
{
    /**
     * Persist client intake from session draft to database
     * POST /api/client/intake/persist
     */
    public function persist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'responses' => 'required|array',
            'summary' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Please sign in to continue.',
            ], 401);
        }

        try {
            $intake = ClientIntake::create([
                'user_id' => $user->id,
                'responses' => $request->responses,
                'summary' => $request->summary,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'intake' => [
                        'id' => $intake->id,
                        'user_id' => $intake->user_id,
                        'created_at' => $intake->created_at->toISOString(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save intake. Please try again.',
            ], 500);
        }
    }

    /**
     * Get user's client intakes
     * GET /api/client/intake
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Please sign in to continue.',
            ], 401);
        }

        $intakes = ClientIntake::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'intakes' => $intakes->map(function ($intake) {
                    return [
                        'id' => $intake->id,
                        'responses' => $intake->responses,
                        'summary' => $intake->summary,
                        'created_at' => $intake->created_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }
}
