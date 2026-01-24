<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\PatientProfile;
use App\Models\User;

class FeedbackController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $query = Feedback::where('patient_id', $patient->id)
            ->with(['sourceUser', 'feedbackable']);

        // Apply filters
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('feedbackable_type')) {
            $query->where('feedbackable_type', $request->feedbackable_type);
        }

        if ($request->filled('date_from')) {
            $query->where('feedback_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('feedback_date', '<=', $request->date_to);
        }

        $feedbacks = $query->orderBy('feedback_date', 'desc')->paginate(15);

        // Get unique feedbackable types for this patient
        $feedbackableTypes = Feedback::where('patient_id', $patient->id)
            ->select('feedbackable_type')
            ->distinct()
            ->pluck('feedbackable_type');

        // Calculate statistics
        $stats = [
            'total' => Feedback::where('patient_id', $patient->id)->count(),
            'by_source' => [
                'parent' => Feedback::where('patient_id', $patient->id)->where('source', 'parent')->count(),
                'self' => Feedback::where('patient_id', $patient->id)->where('source', 'self')->count(),
                'others' => Feedback::where('patient_id', $patient->id)->where('source', 'others')->count(),
                'therapist' => Feedback::where('patient_id', $patient->id)->where('source', 'therapist')->count(),
            ],
            'avg_rating' => round(Feedback::where('patient_id', $patient->id)->whereNotNull('rating')->avg('rating'), 2),
        ];

        return view('doctor.patients.feedback.index', compact('patient', 'feedbacks', 'feedbackableTypes', 'stats'));
    }

    public function show(Request $request, PatientProfile $patient, Feedback $feedback)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Verify feedback belongs to this patient
        if ($feedback->patient_id != $patient->id) {
            abort(404);
        }

        $feedback->load(['sourceUser', 'feedbackable']);

        // Get related feedback for the same feedbackable item
        $relatedFeedback = Feedback::where('feedbackable_type', $feedback->feedbackable_type)
            ->where('feedbackable_id', $feedback->feedbackable_id)
            ->where('id', '!=', $feedback->id)
            ->with('sourceUser')
            ->latest('feedback_date')
            ->limit(5)
            ->get();

        return view('doctor.patients.feedback.show', compact('patient', 'feedback', 'relatedFeedback'));
    }

    public function respond(Request $request, Feedback $feedback)
    {
        $user = $request->user();
        $patient = $feedback->patient;
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $request->validate([
            'response_text' => 'required|string|max:1000',
        ]);

        // Store response in custom_data field
        $customData = $feedback->custom_data ?? [];
        $customData['doctor_responses'] = $customData['doctor_responses'] ?? [];
        $customData['doctor_responses'][] = [
            'doctor_id' => $user->id,
            'doctor_name' => $user->full_name,
            'response' => $request->response_text,
            'responded_at' => now()->toDateTimeString(),
        ];

        $feedback->update(['custom_data' => $customData]);

        return redirect()->back()
            ->with('success', 'Your response has been added successfully.');
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
