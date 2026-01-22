<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientMessage;
use Illuminate\Http\Request;

class PatientMessageController extends Controller
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
     * Display a listing of messages for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $messages = PatientMessage::where('patient_id', $patientId)
            ->with(['doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.messages.index', compact('messages'));
    }
}
