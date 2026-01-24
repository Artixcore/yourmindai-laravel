<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeworkAssignment;

class HomeworkTemplateController extends Controller
{
    /**
     * Show all homework assignments system-wide.
     */
    public function index(Request $request)
    {
        $query = HomeworkAssignment::with(['patient.user', 'assignedByDoctor']);

        // Filter by homework type
        if ($request->filled('homework_type')) {
            $query->where('homework_type', $request->homework_type);
        }

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
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        $homework = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistics by type
        $statsByType = [];
        $homeworkTypes = [
            'psychotherapy', 'lifestyle_modification', 'sleep_tracking',
            'mood_tracking', 'personal_journal', 'risk_tracking',
            'contingency', 'exercise', 'parent_role', 'others_role', 'self_help_tools'
        ];

        foreach ($homeworkTypes as $type) {
            $statsByType[$type] = HomeworkAssignment::where('homework_type', $type)->count();
        }

        $stats = [
            'total' => HomeworkAssignment::count(),
            'assigned' => HomeworkAssignment::where('status', 'assigned')->count(),
            'in_progress' => HomeworkAssignment::where('status', 'in_progress')->count(),
            'completed' => HomeworkAssignment::where('status', 'completed')->count(),
            'by_type' => $statsByType,
        ];

        return view('admin.homework.index', compact('homework', 'stats'));
    }

    /**
     * Show specific homework details.
     */
    public function show($homeworkId)
    {
        $homework = HomeworkAssignment::with([
            'patient.user',
            'assignedByDoctor',
            'completions',
            'feedback',
            'practiceProgressions'
        ])->findOrFail($homeworkId);

        return view('admin.homework.show', compact('homework'));
    }

    /**
     * Get analytics for homework completion rates.
     */
    public function analytics(Request $request)
    {
        // Calculate completion rates by type
        $completionRates = [];
        $homeworkTypes = [
            'psychotherapy', 'lifestyle_modification', 'sleep_tracking',
            'mood_tracking', 'personal_journal', 'risk_tracking',
            'contingency', 'exercise', 'parent_role', 'others_role', 'self_help_tools'
        ];

        foreach ($homeworkTypes as $type) {
            $total = HomeworkAssignment::where('homework_type', $type)->count();
            $completed = HomeworkAssignment::where('homework_type', $type)
                ->where('status', 'completed')
                ->count();
            
            $completionRates[$type] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
        }

        return view('admin.homework.analytics', compact('completionRates'));
    }
}
