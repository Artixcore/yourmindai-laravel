<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    /**
     * Patient login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials.',
            ], 401);
        }

        if ($patient->status !== 'active') {
            return response()->json([
                'success' => false,
                'error' => 'Your account is inactive.',
            ], 403);
        }

        // Create Sanctum token
        $token = $patient->createToken('patient-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'photo_url' => $patient->photo_url,
                    'status' => $patient->status,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Patient logout
     */
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
