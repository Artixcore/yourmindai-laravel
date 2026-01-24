<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ReferralController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Get all referrals for this patient (sent and received)
        $sentReferrals = Referral::where('patient_id', $patient->id)
            ->where('referred_by', $user->id)
            ->with('referredTo')
            ->orderBy('referred_at', 'desc')
            ->get();

        $receivedReferrals = Referral::where('patient_id', $patient->id)
            ->where('referred_to', $user->id)
            ->with('referredBy')
            ->orderBy('referred_at', 'desc')
            ->get();

        return view('doctor.patients.referrals.index', compact('patient', 'sentReferrals', 'receivedReferrals'));
    }

    public function create(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Get all doctors and specialists (excluding current user)
        $specialists = User::where('role', 'doctor')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        $specialties = [
            'psychiatry' => 'Psychiatry',
            'clinical_psychology' => 'Clinical Psychology',
            'counseling_psychology' => 'Counseling Psychology',
            'child_psychology' => 'Child & Adolescent Psychology',
            'neuropsychology' => 'Neuropsychology',
            'substance_abuse' => 'Substance Abuse Counseling',
            'family_therapy' => 'Family Therapy',
            'trauma_therapy' => 'Trauma & PTSD Therapy',
            'eating_disorders' => 'Eating Disorders',
            'behavioral_therapy' => 'Behavioral Therapy',
            'occupational_therapy' => 'Occupational Therapy',
            'social_work' => 'Social Work',
            'other' => 'Other',
        ];

        return view('doctor.patients.referrals.create', compact('patient', 'specialists', 'specialties'));
    }

    public function store(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $validated = $request->validate([
            'referred_to' => 'nullable|exists:users,id',
            'specialty_needed' => 'required|string',
            'reason' => 'required|string',
            'patient_history_summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $validated['patient_id'] = $patient->id;
        $validated['referred_by'] = $user->id;
        $validated['referral_type'] = 'forward';
        $validated['status'] = 'pending';
        $validated['referred_at'] = now();

        // Handle file upload
        if ($request->hasFile('report_file')) {
            $path = $request->file('report_file')->store('referrals', 'public');
            $validated['report_file_path'] = $path;
        }

        $referral = Referral::create($validated);

        return redirect()
            ->route('doctor.patients.referrals.show', [$patient, $referral])
            ->with('success', 'Referral created successfully!');
    }

    public function show(Request $request, PatientProfile $patient, Referral $referral)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        if ($referral->patient_id !== $patient->id) {
            abort(404, 'Referral not found for this patient');
        }

        $referral->load('referredBy', 'referredTo', 'originalReferral', 'backReferrals');

        return view('doctor.patients.referrals.show', compact('patient', 'referral'));
    }

    public function createBackReferral(Request $request, Referral $referral)
    {
        $user = $request->user();
        
        // Only the referred-to doctor can create a back-referral
        if ($referral->referred_to !== $user->id) {
            abort(403, 'Only the receiving specialist can create a back-referral');
        }

        // Can only create back-referral if the original was accepted/in-progress/completed
        if (!in_array($referral->status, ['accepted', 'in_progress', 'completed'])) {
            abort(403, 'Back-referral can only be created for accepted referrals');
        }

        $referral->load('patient', 'referredBy');

        return view('doctor.patients.referrals.create-back-referral', compact('referral'));
    }

    public function storeBackReferral(Request $request, Referral $referral)
    {
        $user = $request->user();
        
        // Only the referred-to doctor can create a back-referral
        if ($referral->referred_to !== $user->id) {
            abort(403, 'Only the receiving specialist can create a back-referral');
        }

        $validated = $request->validate([
            'reason' => 'required|string',
            'patient_history_summary' => 'nullable|string',
            'recommendations' => 'required|string',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $validated['patient_id'] = $referral->patient_id;
        $validated['referred_by'] = $user->id;
        $validated['referred_to'] = $referral->referred_by;
        $validated['referral_type'] = 'back';
        $validated['original_referral_id'] = $referral->id;
        $validated['specialty_needed'] = 'Primary Care';
        $validated['status'] = 'pending';
        $validated['referred_at'] = now();

        // Handle file upload
        if ($request->hasFile('report_file')) {
            $path = $request->file('report_file')->store('referrals', 'public');
            $validated['report_file_path'] = $path;
        }

        $backReferral = Referral::create($validated);

        // Mark original referral as completed
        $referral->complete();

        return redirect()
            ->route('doctor.patients.referrals.show', [$referral->patient_id, $backReferral])
            ->with('success', 'Back-referral created successfully!');
    }

    // Authorization helper
    private function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        // Admin can access all patients
        if ($user->isAdmin()) {
            return true;
        }

        // Doctor can access own patients
        return $user->id === $patient->doctor_id;
    }
}
