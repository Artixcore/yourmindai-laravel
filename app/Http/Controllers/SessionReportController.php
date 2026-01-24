<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SessionReport;
use App\Models\Session;
use App\Models\PatientProfile;

class SessionReportController extends Controller
{
    /**
     * Show form to create session report.
     */
    public function create(Request $request, $sessionId)
    {
        $session = Session::with(['patient', 'doctor'])->findOrFail($sessionId);
        
        // Check authorization
        if ($request->user()->id !== $session->doctor_id && !$request->user()->isAdmin()) {
            abort(403);
        }

        return view('doctor.sessions.report-create', compact('session'));
    }

    /**
     * Store session report.
     */
    public function store(Request $request, $sessionId)
    {
        $session = Session::findOrFail($sessionId);
        
        // Check authorization
        if ($request->user()->id !== $session->doctor_id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'assessments_summary' => 'nullable|string',
            'techniques_assigned' => 'nullable|string',
            'progress_notes' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'shared_with_patient' => 'boolean',
            'shared_with_parents' => 'boolean',
            'shared_with_others' => 'boolean',
            'status' => 'required|in:draft,finalized',
        ]);

        $report = SessionReport::create([
            'session_id' => $session->id,
            'patient_id' => $session->patient_id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'assessments_summary' => $validated['assessments_summary'] ?? null,
            'techniques_assigned' => $validated['techniques_assigned'] ?? null,
            'progress_notes' => $validated['progress_notes'] ?? null,
            'next_steps' => $validated['next_steps'] ?? null,
            'shared_with_patient' => $validated['shared_with_patient'] ?? true,
            'shared_with_parents' => $validated['shared_with_parents'] ?? true,
            'shared_with_others' => $validated['shared_with_others'] ?? false,
            'status' => $validated['status'],
            'finalized_at' => $validated['status'] === 'finalized' ? now() : null,
        ]);

        return redirect()->route('sessions.show', ['patient' => $session->patient_id, 'session' => $session->id])
            ->with('success', 'Session report created successfully!');
    }

    /**
     * Show session report.
     */
    public function show(Request $request, $reportId)
    {
        $report = SessionReport::with(['session', 'patient', 'createdByDoctor', 'feedback'])
            ->findOrFail($reportId);

        $user = $request->user();
        
        // Check if user has access to view this report
        $hasAccess = $user->isAdmin() 
            || $user->id === $report->created_by 
            || ($report->shared_with_patient && $user->id === $report->patient->user_id)
            || ($report->shared_with_parents && $this->isParent($user->id, $report->patient_id))
            || ($report->shared_with_others && $this->isOthersExpert($user->id, $report->patient_id));

        if (!$hasAccess) {
            abort(403);
        }

        return view('client.sessions.report', compact('report'));
    }

    /**
     * Check if user is parent of patient.
     */
    private function isParent($userId, $patientId)
    {
        return \App\Models\ParentLink::where('parent_id', $userId)
            ->where('patient_id', $patientId)
            ->exists();
    }

    /**
     * Check if user is others expert with access to patient.
     */
    private function isOthersExpert($userId, $patientId)
    {
        // Implementation depends on how others/experts are linked to patients
        // For now, return false - implement when referral system is complete
        return false;
    }
}
