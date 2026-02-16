<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use App\Models\PsychometricAssessment;
use App\Models\PsychometricReport;
use App\Models\PsychometricScale;
use App\Models\User;
use Illuminate\Http\Request;

class PsychometricAssessmentController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $assessments = PsychometricAssessment::where('patient_profile_id', $patient->id)
            ->with('scale', 'assignedByDoctor')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available active scales for assignment
        $availableScales = PsychometricScale::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $assessments->count(),
            'pending' => $assessments->where('status', 'pending')->count(),
            'completed' => $assessments->where('status', 'completed')->count(),
            'avg_score' => round($assessments->where('status', 'completed')->avg('total_score'), 2),
        ];

        return view('doctor.patients.psychometric.index', compact('patient', 'assessments', 'availableScales', 'stats'));
    }

    public function assign(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $request->validate([
            'scale_id' => 'required|exists:psychometric_scales,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $scale = PsychometricScale::where('id', $request->scale_id)
            ->where('is_active', true)
            ->firstOrFail();

        PsychometricAssessment::create([
            'patient_profile_id' => $patient->id,
            'scale_id' => $scale->id,
            'assigned_by_doctor_id' => $user->id,
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Assessment assigned successfully to ' . $patient->user->full_name);
    }

    public function show(Request $request, PatientProfile $patient, PsychometricAssessment $assessment)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Verify assessment belongs to this patient
        if ($assessment->patient_profile_id != $patient->id) {
            abort(404);
        }

        $assessment->load('scale', 'assignedByDoctor');

        // Get previous assessments with same scale for comparison
        $previousAssessments = PsychometricAssessment::where('patient_profile_id', $patient->id)
            ->where('scale_id', $assessment->scale_id)
            ->where('id', '!=', $assessment->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(3)
            ->get();

        $report = PsychometricReport::where('assessment_id', $assessment->id)->first();

        return view('doctor.patients.psychometric.show', compact('patient', 'assessment', 'previousAssessments', 'report'));
    }

    public function compare(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Get all completed assessments for this patient
        $assessments = PsychometricAssessment::where('patient_profile_id', $patient->id)
            ->where('status', 'completed')
            ->with('scale')
            ->orderBy('completed_at', 'asc')
            ->get();

        // Group by scale for comparison
        $assessmentsByScale = $assessments->groupBy('scale_id');

        // Get scale filter from request
        $selectedScaleId = $request->input('scale_id');
        
        if ($selectedScaleId) {
            $assessmentsByScale = $assessmentsByScale->filter(function($items, $scaleId) use ($selectedScaleId) {
                return $scaleId == $selectedScaleId;
            });
        }

        // Get available scales that have at least 2 completed assessments
        $comparableScales = PsychometricScale::whereHas('assessments', function($q) use ($patient) {
            $q->where('patient_profile_id', $patient->id)
              ->where('status', 'completed');
        }, '>=', 2)->get();

        // Calculate trends
        $trends = [];
        foreach ($assessmentsByScale as $scaleId => $scaleAssessments) {
            if ($scaleAssessments->count() >= 2) {
                $first = $scaleAssessments->first();
                $last = $scaleAssessments->last();
                
                $trends[$scaleId] = [
                    'scale_name' => $first->scale->name,
                    'first_score' => $first->total_score,
                    'last_score' => $last->total_score,
                    'change' => $last->total_score - $first->total_score,
                    'percent_change' => $first->total_score > 0 
                        ? round((($last->total_score - $first->total_score) / $first->total_score) * 100, 2)
                        : 0,
                    'assessments_count' => $scaleAssessments->count(),
                    'first_date' => $first->completed_at,
                    'last_date' => $last->completed_at,
                ];
            }
        }

        return view('doctor.patients.psychometric.compare', compact(
            'patient',
            'assessmentsByScale',
            'comparableScales',
            'trends',
            'selectedScaleId'
        ));
    }

    protected function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $patient->doctor_id === $user->id || 
                   $user->assignedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }
        
        return false;
    }
}
