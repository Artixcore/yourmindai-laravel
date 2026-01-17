<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Get authenticated patient data
     */
    public function me(Request $request)
    {
        $patient = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'photo_url' => $patient->photo_url,
                'status' => $patient->status,
                'doctor_id' => $patient->doctor_id,
                'created_at' => $patient->created_at->toISOString(),
                'updated_at' => $patient->updated_at->toISOString(),
            ],
        ]);
    }
}
