<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientProfile;
use App\Models\ParentLink;
use App\Models\HomeworkAssignment;
use App\Models\PracticeProgression;
use App\Models\Feedback;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    /**
     * Show parent dashboard.
     */
    public function index(Request $request)
    {
        $parent = $request->user();
        
        // Get all children (patients) linked to this parent
        $children = PatientProfile::whereHas('parentLinks', function ($query) use ($parent) {
            $query->where('parent_id', $parent->id);
        })->with(['user', 'doctor'])->get();

        // Get overview stats for all children
        $stats = [
            'total_children' => $children->count(),
            'pending_feedback' => $this->getPendingFeedbackCount($parent->id),
            'recent_progressions' => $this->getRecentProgressionsCount($parent->id),
        ];

        return view('parent.dashboard', compact('children', 'stats'));
    }

    /**
     * Show specific child's details.
     */
    public function showChild(Request $request, $patientId)
    {
        $parent = $request->user();
        
        // Verify this parent has access to this child
        $patient = PatientProfile::whereHas('parentLinks', function ($query) use ($parent) {
            $query->where('parent_id', $parent->id);
        })->findOrFail($patientId);

        // Get child's homework assignments
        $homeworkAssignments = HomeworkAssignment::where('patient_id', $patientId)
            ->where('status', '!=', 'cancelled')
            ->with(['feedback', 'practiceProgressions'])
            ->latest()
            ->get();

        // Get recent progressions
        $recentProgressions = PracticeProgression::whereHas('progressionable', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->latest('progress_date')->take(10)->get();

        return view('parent.child.profile', compact('patient', 'homeworkAssignments', 'recentProgressions'));
    }

    /**
     * Get pending feedback count for parent.
     */
    private function getPendingFeedbackCount($parentId)
    {
        // Count homework assignments that require parent feedback but don't have it yet
        return HomeworkAssignment::whereHas('patient.parentLinks', function ($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })
        ->where('requires_parent_feedback', true)
        ->where('status', 'in_progress')
        ->whereDoesntHave('feedback', function ($query) use ($parentId) {
            $query->where('source', 'parent')
                  ->where('source_user_id', $parentId);
        })
        ->count();
    }

    /**
     * Get recent progressions count.
     */
    private function getRecentProgressionsCount($parentId)
    {
        $weekAgo = Carbon::now()->subWeek();
        
        return PracticeProgression::whereHas('patient.parentLinks', function ($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })
        ->where('monitored_by', 'parent')
        ->where('progress_date', '>=', $weekAgo)
        ->count();
    }
}
