<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\Assessment;
use Illuminate\Http\Request;

class PatientAssessmentController extends Controller
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
     * Display a listing of assessments for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $assessments = Assessment::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.assessments.index', compact('assessments'));
    }

    /**
     * Display the specified assessment
     */
    public function show($id)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $assessment = Assessment::where('id', $id)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        
        return view('patient.assessments.show', compact('assessment'));
    }
}
