<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;

class PatientAppointmentController extends Controller
{
    /**
     * Get patient profile ID from authenticated user
     * Note: Appointment model uses PatientProfile, not Patient
     */
    private function getPatientProfileId()
    {
        $user = auth()->user();
        
        // First try to get PatientProfile (Appointment uses this)
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        if ($patientProfile) {
            return $patientProfile->id;
        }
        
        // If no PatientProfile, try to find one by email via Patient model
        $patient = Patient::where('email', $user->email)->first();
        if ($patient) {
            // Try to find PatientProfile by matching email or other criteria
            $patientProfile = PatientProfile::where('user_id', $user->id)
                ->orWhere(function($query) use ($patient) {
                    $query->where('full_name', $patient->name)
                          ->orWhere('phone', $patient->phone);
                })
                ->first();
            
            if ($patientProfile) {
                return $patientProfile->id;
            }
        }
        
        return null;
    }

    /**
     * Display a listing of appointments for the authenticated patient
     */
    public function index()
    {
        $patientProfileId = $this->getPatientProfileId();
        
        if (!$patientProfileId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $appointments = Appointment::where('patient_id', $patientProfileId)
            ->with(['doctor', 'patient'])
            ->orderBy('date', 'asc')
            ->get();
        
        return view('patient.appointments.index', compact('appointments'));
    }
}
