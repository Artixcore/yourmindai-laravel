<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\PsychometricAssessment;
use App\Models\PsychometricScale;
use Illuminate\Http\Request;

class PsychometricAssessmentController extends Controller
{
    /**
     * Get PatientProfile from Patient model
     */
    private function getPatientProfile(Patient $patient)
    {
        // Try to find PatientProfile by user_id if patient has a user
        if ($patient->email) {
            $user = \App\Models\User::where('email', $patient->email)->first();
            if ($user) {
                $patientProfile = PatientProfile::where('user_id', $user->id)->first();
                if ($patientProfile) {
                    return $patientProfile;
                }
            }
        }
        
        // Try to find by matching doctor_id and name/email
        $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
            ->where(function($query) use ($patient) {
                $query->where('full_name', $patient->name)
                      ->orWhere('phone', $patient->phone);
            })
            ->first();
        
        return $patientProfile;
    }

    /**
     * Display a listing of assessments for a patient.
     */
    public function index(Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);
        
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found. Please ensure the patient has a profile.');
        }
        
        // Ensure doctor can only see assessments for their patients
        $doctorId = auth()->id();
        $assessments = PsychometricAssessment::where(function($query) use ($patientProfile, $patient) {
                $query->where('patient_profile_id', $patientProfile->id)
                      ->orWhere('patient_id', $patient->id);
            })
            ->where('assigned_by_doctor_id', $doctorId)
            ->with('scale', 'assignedByDoctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.psychometric.index', compact('patient', 'patientProfile', 'assessments'));
    }

    /**
     * Assign a new assessment to a patient.
     */
    public function assign(Request $request, Patient $patient)
    {
        $request->validate([
            'scale_id' => 'required|exists:psychometric_scales,id',
        ]);

        $patientProfile = $this->getPatientProfile($patient);
        
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found. Please ensure the patient has a profile.');
        }

        $scale = PsychometricScale::findOrFail($request->scale_id);

        PsychometricAssessment::create([
            'patient_profile_id' => $patientProfile->id,
            'patient_id' => $patient->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => auth()->id(),
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Assessment assigned successfully.');
    }

    /**
     * Display the specified assessment.
     */
    public function show(Patient $patient, PsychometricAssessment $assessment)
    {
        $patientProfile = $this->getPatientProfile($patient);
        $assessment->load('scale', 'patientProfile', 'assignedByDoctor');
        return view('doctor.patients.psychometric.show', compact('patient', 'patientProfile', 'assessment'));
    }
}
