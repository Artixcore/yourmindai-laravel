<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAssessment;
use App\Models\PatientProfile;

class GeneralAssessmentController extends Controller
{
    /**
     * Show all general assessments system-wide.
     */
    public function index(Request $request)
    {
        $query = GeneralAssessment::with(['patient.user', 'assignedByDoctor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('assigned_by', $request->doctor_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('assigned_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('assigned_at', '<=', $request->end_date);
        }

        $assessments = $query->orderBy('assigned_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total' => GeneralAssessment::count(),
            'pending' => GeneralAssessment::where('status', 'pending')->count(),
            'in_progress' => GeneralAssessment::where('status', 'in_progress')->count(),
            'completed' => GeneralAssessment::where('status', 'completed')->count(),
        ];

        return view('admin.general-assessments.index', compact('assessments', 'stats'));
    }

    /**
     * Show specific assessment details.
     */
    public function show($assessmentId)
    {
        $assessment = GeneralAssessment::with([
            'patient.user',
            'assignedByDoctor',
            'questions',
            'responses',
            'feedback'
        ])->findOrFail($assessmentId);

        return view('admin.general-assessments.show', compact('assessment'));
    }

    /**
     * Export assessments data.
     */
    public function export(Request $request)
    {
        // Implementation for CSV/Excel export
        // This would generate a downloadable file with assessment data
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
