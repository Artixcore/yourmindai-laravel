<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Feedback::class);
        
        $query = Feedback::with(['patient.user', 'sourceUser', 'feedbackable']);

        // Apply filters
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('date_from')) {
            $query->where('feedback_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('feedback_date', '<=', $request->date_to);
        }

        if ($request->filled('feedbackable_type')) {
            $query->where('feedbackable_type', $request->feedbackable_type);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->where('feedback_text', 'like', '%' . $request->search . '%');
        }

        $feedbacks = $query->orderBy('feedback_date', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Get unique feedbackable types
        $feedbackableTypes = Feedback::select('feedbackable_type')
            ->distinct()
            ->pluck('feedbackable_type');

        // Calculate statistics
        $stats = [
            'total' => Feedback::count(),
            'by_source' => [
                'parent' => Feedback::where('source', 'parent')->count(),
                'self' => Feedback::where('source', 'self')->count(),
                'others' => Feedback::where('source', 'others')->count(),
                'therapist' => Feedback::where('source', 'therapist')->count(),
            ],
            'avg_rating' => round(Feedback::whereNotNull('rating')->avg('rating'), 2),
            'this_week' => Feedback::where('feedback_date', '>=', Carbon::now()->startOfWeek())->count(),
            'this_month' => Feedback::whereMonth('feedback_date', Carbon::now()->month)->count(),
        ];

        return view('admin.feedback.index', compact('feedbacks', 'patients', 'feedbackableTypes', 'stats'));
    }

    public function show($id)
    {
        $feedback = Feedback::with([
            'patient.user',
            'sourceUser',
            'feedbackable'
        ])->findOrFail($id);
        
        $this->authorize('view', $feedback);

        // Get related feedback for the same feedbackable item
        $relatedFeedback = Feedback::where('feedbackable_type', $feedback->feedbackable_type)
            ->where('feedbackable_id', $feedback->feedbackable_id)
            ->where('id', '!=', $feedback->id)
            ->with('sourceUser')
            ->latest('feedback_date')
            ->limit(5)
            ->get();

        return view('admin.feedback.show', compact('feedback', 'relatedFeedback'));
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $this->authorize('delete', $feedback);
        
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Feedback::with(['patient.user', 'sourceUser', 'feedbackable']);

        // Apply same filters as index
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('date_from')) {
            $query->where('feedback_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('feedback_date', '<=', $request->date_to);
        }

        $feedbacks = $query->orderBy('feedback_date', 'desc')->get();

        // Generate CSV
        $filename = 'feedback_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($feedbacks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Date',
                'Patient',
                'Source',
                'Source User',
                'Feedbackable Type',
                'Rating',
                'Feedback Text'
            ]);

            // Add data rows
            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->id,
                    $feedback->feedback_date ? $feedback->feedback_date->format('Y-m-d H:i:s') : 'N/A',
                    $feedback->patient->user->full_name ?? 'N/A',
                    $feedback->source,
                    $feedback->sourceUser->full_name ?? 'Anonymous',
                    class_basename($feedback->feedbackable_type),
                    $feedback->rating ?? 'N/A',
                    $feedback->feedback_text ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
