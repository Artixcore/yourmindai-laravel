<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\InviteCode;
use App\Models\ParentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Generate a sequential patient number (PAT-0001, PAT-0002, etc.)
     */
    private function generatePatientNumber(): string
    {
        $lastPatient = PatientProfile::orderBy('patient_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastPatient && $lastPatient->patient_number) {
            if (preg_match('/PAT-(\d+)/', $lastPatient->patient_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }
        }

        return 'PAT-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * POST /api/auth/register-doctor
     * Register a new doctor account
     */
    public function registerDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'fullName' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'error' => 'This email is already in use. Try signing in instead.',
            ], 400);
        }

        $user = User::create([
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role' => 'DOCTOR',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => (string) $user->_id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'createdAt' => $user->created_at->toISOString(),
                    'updatedAt' => $user->updated_at->toISOString(),
                ],
                'message' => "Doctor account created for {$request->fullName}",
            ],
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Authenticate user and return JWT
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $isEmail = str_contains($request->identifier, '@');
        
        $user = $isEmail
            ? User::where('email', $request->identifier)->first()
            : User::where('username', $request->identifier)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect email or password. Please try again.',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => (string) $user->_id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'createdAt' => $user->created_at->toISOString(),
                    'updatedAt' => $user->updated_at->toISOString(),
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/register-patient
     * Self-register as a patient using an invite code
     */
    public function registerPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inviteCode' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'fullName' => 'required|string',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE,OTHER,PREFER_NOT_TO_SAY',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $invite = InviteCode::where('code', $request->inviteCode)->first();

        if (!$invite) {
            return response()->json([
                'success' => false,
                'error' => "This invite code doesn't exist. Please check and try again.",
            ], 400);
        }

        if ($invite->used) {
            return response()->json([
                'success' => false,
                'error' => 'This invite code has already been used.',
            ], 400);
        }

        if ($invite->type !== 'PATIENT') {
            return response()->json([
                'success' => false,
                'error' => 'This invite code is not valid for this type of registration.',
            ], 400);
        }

        if (now()->greaterThan($invite->expires_at)) {
            return response()->json([
                'success' => false,
                'error' => 'This invite code has expired. Please request a new one from your healthcare provider.',
            ], 400);
        }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'error' => 'This email is already in use. Try signing in instead.',
            ], 400);
        }

        $patientNumber = $this->generatePatientNumber();

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'role' => 'PATIENT',
            ]);

            $profile = PatientProfile::create([
                'patient_number' => $patientNumber,
                'user_id' => $user->_id,
                'doctor_id' => $invite->creator_id,
                'full_name' => $request->fullName,
                'date_of_birth' => $request->dateOfBirth,
                'gender' => $request->gender,
                'status' => 'ACTIVE',
            ]);

            $invite->update([
                'used' => true,
                'used_at' => now(),
                'patient_id' => $profile->_id,
            ]);

            DB::commit();

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => (string) $user->_id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'createdAt' => $user->created_at->toISOString(),
                        'updatedAt' => $user->updated_at->toISOString(),
                    ],
                    'profile' => [
                        'id' => (string) $profile->_id,
                        'patientNumber' => $profile->patient_number,
                        'fullName' => $profile->full_name,
                        'dateOfBirth' => $profile->date_of_birth->format('Y-m-d'),
                        'gender' => $profile->gender,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/register-parent
     * Register as a parent using an invite code
     */
    public function registerParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inviteCode' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $invite = InviteCode::where('code', $request->inviteCode)
            ->with('patient')
            ->first();

        if (!$invite) {
            return response()->json([
                'success' => false,
                'error' => "This invite code doesn't exist. Please check and try again.",
            ], 400);
        }

        if ($invite->used) {
            return response()->json([
                'success' => false,
                'error' => 'This invite code has already been used.',
            ], 400);
        }

        if ($invite->type !== 'PARENT') {
            return response()->json([
                'success' => false,
                'error' => 'This invite code is not valid for this type of registration.',
            ], 400);
        }

        if (now()->greaterThan($invite->expires_at)) {
            return response()->json([
                'success' => false,
                'error' => 'This invite code has expired. Please request a new one from your healthcare provider.',
            ], 400);
        }

        if (!$invite->patient_id) {
            return response()->json([
                'success' => false,
                'error' => 'This invite code is not linked to any patient. Please contact your healthcare provider.',
            ], 400);
        }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'error' => 'This email is already in use. Try signing in instead.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'role' => 'PARENT',
            ]);

            ParentLink::create([
                'parent_id' => $user->_id,
                'patient_id' => $invite->patient_id,
            ]);

            $invite->update([
                'used' => true,
                'used_at' => now(),
            ]);

            DB::commit();

            $patient = PatientProfile::find($invite->patient_id);
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => (string) $user->_id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'createdAt' => $user->created_at->toISOString(),
                        'updatedAt' => $user->updated_at->toISOString(),
                    ],
                    'linkedPatient' => $patient ? [
                        'id' => (string) $patient->_id,
                        'fullName' => $patient->full_name,
                    ] : null,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /api/auth/me
     * Get current user profile
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => (string) $user->_id,
                    'email' => $user->email,
                    'username' => $user->username,
                    'role' => $user->role,
                    'createdAt' => $user->created_at->toISOString(),
                    'updatedAt' => $user->updated_at->toISOString(),
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     * Logout current user (client should discard token)
     */
    public function logout(Request $request)
    {
        // JWT is stateless, so we just return success
        // Client should discard the token
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
