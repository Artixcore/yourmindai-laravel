<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorInstruction;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Generate a sequential patient number (PAT-0001, PAT-0002, etc.)
     */
    private function generatePatientNumber(): string
    {
        $lastPatient = PatientProfile::orderBy('patient_number', 'desc')->first();

        $nextNumber = 1;
        if ($lastPatient && $lastPatient->patient_number) {
            if (preg_match('/PAT-(\d+)/', $lastPatient->patient_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }
        }

        return 'PAT-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a temporary password
     */
    private function generateTempPassword(): string
    {
        return bin2hex(random_bytes(4)) . 'A1!';
    }

    /**
     * Generate username from full name
     */
    private function generateUsername(string $fullName): string
    {
        $parts = explode(' ', strtolower($fullName));
        $username = $parts[0];
        if (count($parts) > 1) {
            $username .= substr($parts[1], 0, 1);
        }
        $username .= rand(100, 999);
        return $username;
    }

    /**
     * GET /api/doctors/patients
     * List all patients for the authenticated doctor
     */
    public function listPatients(Request $request)
    {
        $doctorId = $request->user()->_id;

        $patients = PatientProfile::where('doctor_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'patients' => $patients->map(function ($p) {
                    return [
                        'id' => (string) $p->_id,
                        'patientNumber' => $p->patient_number,
                        'userId' => (string) $p->user_id,
                        'fullName' => $p->full_name,
                        'dateOfBirth' => $p->date_of_birth->format('Y-m-d'),
                        'gender' => $p->gender,
                        'phone' => $p->phone,
                        'problem' => $p->problem,
                        'status' => $p->status,
                        'createdAt' => $p->created_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * POST /api/doctors/patients
     * Create a new patient account
     */
    public function createPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE,OTHER,PREFER_NOT_TO_SAY',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username',
            'phone' => 'nullable|string',
            'problem' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $doctorId = $request->user()->_id;
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'error' => 'This email is already in use.',
            ], 400);
        }

        $finalUsername = $request->username ?? $this->generateUsername($request->fullName);
        $usernameGenerated = !$request->username;

        if (User::where('username', $finalUsername)->exists()) {
            $finalUsername = $this->generateUsername($request->fullName);
        }

        $tempPassword = $request->password ?? $this->generateTempPassword();
        $patientNumber = $this->generatePatientNumber();

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'username' => $finalUsername,
                'password_hash' => Hash::make($tempPassword),
                'role' => 'PATIENT',
            ]);

            $profile = PatientProfile::create([
                'patient_number' => $patientNumber,
                'user_id' => $user->_id,
                'doctor_id' => $doctorId,
                'full_name' => $request->fullName,
                'date_of_birth' => $request->dateOfBirth,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'problem' => $request->problem,
                'status' => 'ACTIVE',
            ]);

            DB::commit();

            $response = [
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
                    'profile' => [
                        'id' => (string) $profile->_id,
                        'patientNumber' => $profile->patient_number,
                        'userId' => (string) $profile->user_id,
                        'doctorId' => (string) $profile->doctor_id,
                        'fullName' => $profile->full_name,
                        'dateOfBirth' => $profile->date_of_birth->format('Y-m-d'),
                        'gender' => $profile->gender,
                        'phone' => $profile->phone,
                        'problem' => $profile->problem,
                        'status' => $profile->status,
                        'createdAt' => $profile->created_at->toISOString(),
                        'updatedAt' => $profile->updated_at->toISOString(),
                    ],
                    'patientNumber' => $profile->patient_number,
                ],
            ];

            if (!$request->password) {
                $response['data']['temporaryPassword'] = $tempPassword;
            }
            if ($usernameGenerated) {
                $response['data']['generatedUsername'] = $finalUsername;
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to create patient.',
            ], 500);
        }
    }

    /**
     * PUT /api/doctors/patients/{id}
     * Update a patient's profile
     */
    public function updatePatient(Request $request, string $id)
    {
        // TODO: Implement patient update
        return response()->json([
            'success' => false,
            'error' => 'Not implemented yet',
        ], 501);
    }

    /**
     * DELETE /api/doctors/patients/{id}
     * Soft delete (archive) a patient
     */
    public function deletePatient(Request $request, string $id)
    {
        $doctorId = $request->user()->_id;
        $patient = PatientProfile::find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'error' => "We couldn't find this patient. They may have been removed.",
            ], 404);
        }

        if ((string) $patient->doctor_id !== (string) $doctorId) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to access this patient's records.",
            ], 403);
        }

        $patient->update(['status' => 'ARCHIVED']);

        return response()->json([
            'success' => true,
            'message' => 'Patient archived successfully',
        ]);
    }

    /**
     * POST /api/doctors/patients/{id}/reset-password
     * Reset a patient's password
     */
    public function resetPassword(Request $request, string $id)
    {
        $doctorId = $request->user()->_id;
        $patient = PatientProfile::find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'error' => "We couldn't find this patient. They may have been removed.",
            ], 404);
        }

        if ((string) $patient->doctor_id !== (string) $doctorId) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to access this patient's records.",
            ], 403);
        }

        $tempPassword = $this->generateTempPassword();
        $user = User::find($patient->user_id);
        $user->update(['password_hash' => Hash::make($tempPassword)]);

        return response()->json([
            'success' => true,
            'data' => [
                'temporaryPassword' => $tempPassword,
                'message' => 'Password reset successfully',
            ],
        ]);
    }
}
