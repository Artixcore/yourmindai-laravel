<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientMedication;
use Illuminate\Http\Request;

class PatientMedicationController extends Controller
{
    /**
     * Get patient ID from authenticated user
     */
    private function getPatientId()
    {
        $user = auth()->user();
        
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        
        if ($patientProfile) {
            return $patientProfile->id;
        }
        
        if ($patient) {
            return $patient->id;
        }
        
        return null;
    }

    /**
     * Display a listing of medications for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        // Get medications for the patient
        $medications = \App\Models\PatientMedication::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.medications.index', compact('medications'));
    }
}
