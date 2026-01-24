<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\Session;
use App\Models\User;
use App\Services\ReviewQuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientReviewController extends Controller
{
    protected $reviewQuestionService;

    public function __construct(ReviewQuestionService $reviewQuestionService)
    {
        $this->reviewQuestionService = $reviewQuestionService;
    }

    /**
     * Display a listing of the patient's reviews
     */
    public function index(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $patient = Patient::find($patientId['id']);
        
        // Get all reviews by this patient
        $reviews = Review::where('patient_id', $patientId['id'])
            ->with(['doctor', 'session', 'answers.question'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get reviewable items (sessions not yet reviewed and doctor)
        $unreviewedSessions = Session::where('patient_id', $patientId['id'])
            ->whereDoesntHave('reviews', function ($q) use ($patientId) {
                $q->where('patient_id', $patientId['id'])
                  ->where('review_type', 'session');
            })
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if doctor has been reviewed
        $doctorReviewed = Review::where('patient_id', $patientId['id'])
            ->where('doctor_id', $patient->doctor_id)
            ->where('review_type', 'doctor')
            ->exists();

        return view('client.reviews.index', compact(
            'reviews',
            'unreviewedSessions',
            'doctorReviewed',
            'patient'
        ));
    }

    /**
     * Show the form for creating a new review
     */
    public function create(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $patient = Patient::with('doctor')->find($patientId['id']);
        
        // Get review type and session (if applicable)
        $reviewType = $request->input('type', 'doctor');
        $sessionId = $request->input('session_id');
        
        $session = null;
        if ($reviewType === 'session' && $sessionId) {
            $session = Session::where('id', $sessionId)
                ->where('patient_id', $patientId['id'])
                ->with('doctor')
                ->firstOrFail();
            
            // Check if session already reviewed
            if ($session->hasBeenReviewed()) {
                return redirect()->route('client.reviews.index')
                    ->with('error', 'You have already reviewed this session.');
            }
        }

        // Check if doctor already reviewed (for doctor reviews)
        if ($reviewType === 'doctor') {
            $existingReview = Review::where('patient_id', $patientId['id'])
                ->where('doctor_id', $patient->doctor_id)
                ->where('review_type', 'doctor')
                ->first();
            
            if ($existingReview) {
                return redirect()->route('client.reviews.index')
                    ->with('error', 'You have already reviewed your doctor.');
            }
        }

        // Get dynamic questions based on patient profile
        $questions = $this->reviewQuestionService->getQuestionsForReview(
            $reviewType,
            $patient,
            $session
        );

        return view('client.reviews.create', compact(
            'patient',
            'reviewType',
            'session',
            'questions'
        ));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $patient = Patient::find($patientId['id']);
        
        $request->validate([
            'review_type' => 'required|in:doctor,session',
            'session_id' => 'nullable|exists:therapy_sessions,id',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
            'is_anonymous' => 'boolean',
            'answers' => 'required|array',
        ]);

        // Get questions to validate answers
        $session = $request->session_id ? Session::find($request->session_id) : null;
        $questions = $this->reviewQuestionService->getQuestionsForReview(
            $request->review_type,
            $patient,
            $session
        );

        // Validate answers
        $validationErrors = $this->reviewQuestionService->validateAnswers(
            $request->answers,
            $questions
        );

        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors)->withInput();
        }

        // Check for duplicate reviews
        $existingReview = Review::where('patient_id', $patientId['id'])
            ->where('doctor_id', $patient->doctor_id)
            ->where('review_type', $request->review_type);
        
        if ($request->review_type === 'session' && $request->session_id) {
            $existingReview->where('session_id', $request->session_id);
        }
        
        if ($existingReview->exists()) {
            return back()->with('error', 'You have already submitted a review for this.');
        }

        try {
            DB::beginTransaction();

            // Create review
            $review = Review::create([
                'patient_id' => $patientId['id'],
                'doctor_id' => $patient->doctor_id,
                'session_id' => $request->session_id,
                'review_type' => $request->review_type,
                'overall_rating' => $request->overall_rating,
                'comment' => $request->comment,
                'is_anonymous' => $request->is_anonymous ?? false,
                'status' => 'published',
            ]);

            // Save answers
            foreach ($request->answers as $questionId => $answerValue) {
                if (!empty($answerValue)) {
                    ReviewAnswer::create([
                        'review_id' => $review->id,
                        'question_id' => $questionId,
                        'answer_value' => is_array($answerValue) ? json_encode($answerValue) : $answerValue,
                        'answer_text' => $request->input("answer_text.{$questionId}"),
                    ]);
                }
            }

            DB::commit();

            // Clear doctor rating cache
            cache()->forget("doctor_{$patient->doctor_id}_avg_rating");

            return redirect()->route('client.reviews.index')
                ->with('success', 'Thank you for your feedback!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit review. Please try again.')->withInput();
        }
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId || $review->patient_id !== $patientId['id']) {
            abort(403, 'Unauthorized access.');
        }

        $review->load(['doctor', 'session', 'answers.question.options']);

        return view('client.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified review
     */
    public function edit(Review $review)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId || $review->patient_id !== $patientId['id']) {
            abort(403, 'Unauthorized access.');
        }

        // Check if review can still be edited (within 48 hours)
        if (!$review->canBeEdited()) {
            return redirect()->route('client.reviews.show', $review)
                ->with('error', 'This review can no longer be edited (48-hour window has passed).');
        }

        $patient = Patient::find($patientId['id']);
        $session = $review->session;
        
        // Get questions
        $questions = $this->reviewQuestionService->getQuestionsForReview(
            $review->review_type,
            $patient,
            $session
        );

        // Get existing answers
        $existingAnswers = $review->answers->keyBy('question_id');

        return view('client.reviews.edit', compact(
            'review',
            'patient',
            'session',
            'questions',
            'existingAnswers'
        ));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId || $review->patient_id !== $patientId['id']) {
            abort(403, 'Unauthorized access.');
        }

        // Check if review can still be edited
        if (!$review->canBeEdited()) {
            return redirect()->route('client.reviews.show', $review)
                ->with('error', 'This review can no longer be edited (48-hour window has passed).');
        }

        $request->validate([
            'overall_rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
            'is_anonymous' => 'boolean',
            'answers' => 'required|array',
        ]);

        $patient = Patient::find($patientId['id']);
        $session = $review->session;
        
        // Get questions to validate answers
        $questions = $this->reviewQuestionService->getQuestionsForReview(
            $review->review_type,
            $patient,
            $session
        );

        // Validate answers
        $validationErrors = $this->reviewQuestionService->validateAnswers(
            $request->answers,
            $questions
        );

        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors)->withInput();
        }

        try {
            DB::beginTransaction();

            // Update review
            $review->update([
                'overall_rating' => $request->overall_rating,
                'comment' => $request->comment,
                'is_anonymous' => $request->is_anonymous ?? false,
            ]);

            // Delete old answers
            $review->answers()->delete();

            // Save new answers
            foreach ($request->answers as $questionId => $answerValue) {
                if (!empty($answerValue)) {
                    ReviewAnswer::create([
                        'review_id' => $review->id,
                        'question_id' => $questionId,
                        'answer_value' => is_array($answerValue) ? json_encode($answerValue) : $answerValue,
                        'answer_text' => $request->input("answer_text.{$questionId}"),
                    ]);
                }
            }

            DB::commit();

            // Clear doctor rating cache
            cache()->forget("doctor_{$patient->doctor_id}_avg_rating");

            return redirect()->route('client.reviews.show', $review)
                ->with('success', 'Review updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update review. Please try again.')->withInput();
        }
    }

    /**
     * Check if the patient is eligible to leave reviews
     */
    public function checkEligibility(Request $request)
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            return response()->json(['eligible' => false]);
        }

        $patient = Patient::find($patientId['id']);
        
        // Count unreviewed sessions
        $unreviewedSessionCount = Session::where('patient_id', $patientId['id'])
            ->whereDoesntHave('reviews', function ($q) use ($patientId) {
                $q->where('patient_id', $patientId['id']);
            })
            ->count();

        // Check if doctor has been reviewed
        $doctorReviewed = Review::where('patient_id', $patientId['id'])
            ->where('doctor_id', $patient->doctor_id)
            ->where('review_type', 'doctor')
            ->exists();

        return response()->json([
            'eligible' => true,
            'unreviewed_sessions' => $unreviewedSessionCount,
            'doctor_reviewed' => $doctorReviewed,
        ]);
    }

    /**
     * Show public reviews for a doctor
     */
    public function doctorReviews(User $doctor)
    {
        if (!$doctor->isDoctor()) {
            abort(404);
        }

        $reviews = Review::where('doctor_id', $doctor->id)
            ->where('review_type', 'doctor')
            ->published()
            ->with(['patient', 'answers.question'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $doctor->cached_average_rating;
        
        // Get rating distribution
        $ratingDistribution = Review::where('doctor_id', $doctor->id)
            ->where('review_type', 'doctor')
            ->published()
            ->selectRaw('overall_rating, COUNT(*) as count')
            ->groupBy('overall_rating')
            ->orderBy('overall_rating', 'desc')
            ->pluck('count', 'overall_rating')
            ->toArray();

        return view('client.doctors.reviews', compact(
            'doctor',
            'reviews',
            'averageRating',
            'ratingDistribution'
        ));
    }

    /**
     * Get the authenticated patient ID
     */
    private function getPatientId()
    {
        $user = auth()->user();
        
        // Try to get patient profile
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        
        // Try to get patient model
        $patient = Patient::where('email', $user->email)->first();
        
        if ($patientProfile) {
            return ['id' => $patientProfile->id, 'is_profile' => true];
        } elseif ($patient) {
            return ['id' => $patient->id, 'is_profile' => false];
        }
        
        return null;
    }
}
