<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\Session;
use Illuminate\Http\Request;

class PatientSessionController extends Controller
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
     * Check if called from client route
     */
    private function isClientRoute()
    {
        return request()->routeIs('client.*');
    }

    /**
     * Get appropriate dashboard route
     */
    private function getDashboardRoute()
    {
        return $this->isClientRoute() ? 'client.dashboard' : 'patient.dashboard';
    }

    /**
     * Display a listing of sessions for the authenticated patient
     */
    public function index()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route($this->getDashboardRoute())
                ->with('error', 'Patient profile not found.');
        }
        
        $sessions = Session::where('patient_id', $patientId)
            ->with(['doctor', 'days'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $view = $this->isClientRoute() ? 'client.sessions.index' : 'patient.sessions.index';
        return view($view, compact('sessions'));
    }

    /**
     * Display the specified session
     */
    public function show($id)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route($this->getDashboardRoute())
                ->with('error', 'Patient profile not found.');
        }
        
        $session = Session::where('id', $id)
            ->where('patient_id', $patientId)
            ->with(['doctor', 'days' => function ($query) {
                $query->orderBy('day_date', 'desc');
            }])
            ->firstOrFail();
        
        $view = $this->isClientRoute() ? 'client.sessions.show' : 'patient.sessions.show';
        return view($view, compact('session'));
    }
}
