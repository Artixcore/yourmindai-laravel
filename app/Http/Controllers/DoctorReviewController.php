<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorReviewController extends Controller
{
    /**
     * Display a listing of the doctor's reviews
     */
    public function index(Request $request)
    {
        $doctor = auth()->user();

        if (!$doctor->isDoctor()) {
            abort(403, 'Only doctors can access this page.');
        }

        $query = Review::where('doctor_id', $doctor->id)
            ->with(['patient', 'session', 'answers.question'])
            ->orderBy('created_at', 'desc');

        // Filter by review type
        if ($request->has('type') && in_array($request->type, ['doctor', 'session'])) {
            $query->where('review_type', $request->type);
        }

        // Filter by rating
        if ($request->has('rating') && is_numeric($request->rating)) {
            $query->where('overall_rating', '>=', $request->rating);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by session
        if ($request->has('session_id') && $request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        $reviews = $query->paginate(15);

        // Get statistics
        $stats = $this->getReviewStats($doctor->id);

        // Get recent sessions for filtering
        $sessions = $doctor->sessions()
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('doctor.reviews.index', compact(
            'reviews',
            'stats',
            'sessions'
        ));
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $doctor = auth()->user();

        // Ensure the review belongs to this doctor
        if ($review->doctor_id !== $doctor->id) {
            abort(403, 'You do not have permission to view this review.');
        }

        $review->load([
            'patient',
            'session.patient',
            'answers.question.options'
        ]);

        return view('doctor.reviews.show', compact('review'));
    }

    /**
     * Show review analytics
     */
    public function analytics(Request $request)
    {
        $doctor = auth()->user();

        if (!$doctor->isDoctor()) {
            abort(403, 'Only doctors can access this page.');
        }

        // Get overall statistics
        $stats = $this->getReviewStats($doctor->id);

        // Get rating distribution
        $ratingDistribution = Review::where('doctor_id', $doctor->id)
            ->where('review_type', 'doctor')
            ->published()
            ->selectRaw('overall_rating, COUNT(*) as count')
            ->groupBy('overall_rating')
            ->orderBy('overall_rating', 'desc')
            ->get();

        // Get reviews over time (last 12 months)
        $reviewsOverTime = Review::where('doctor_id', $doctor->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, AVG(overall_rating) as avg_rating')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get most recent reviews
        $recentReviews = Review::where('doctor_id', $doctor->id)
            ->with(['patient', 'session'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get question-specific analytics
        $questionAnalytics = $this->getQuestionAnalytics($doctor->id);

        return view('doctor.reviews.analytics', compact(
            'stats',
            'ratingDistribution',
            'reviewsOverTime',
            'recentReviews',
            'questionAnalytics'
        ));
    }

    /**
     * Get review statistics for a doctor
     */
    private function getReviewStats($doctorId)
    {
        $allReviews = Review::where('doctor_id', $doctorId)->published();
        $doctorReviews = Review::where('doctor_id', $doctorId)
            ->where('review_type', 'doctor')
            ->published();
        $sessionReviews = Review::where('doctor_id', $doctorId)
            ->where('review_type', 'session')
            ->published();

        return [
            'total_reviews' => $allReviews->count(),
            'doctor_reviews' => $doctorReviews->count(),
            'session_reviews' => $sessionReviews->count(),
            'average_rating' => round($allReviews->avg('overall_rating') ?? 0, 2),
            'doctor_average_rating' => round($doctorReviews->avg('overall_rating') ?? 0, 2),
            'session_average_rating' => round($sessionReviews->avg('overall_rating') ?? 0, 2),
            'five_star_count' => $allReviews->where('overall_rating', '>=', 5)->count(),
            'four_star_count' => $allReviews->whereBetween('overall_rating', [4, 4.99])->count(),
            'three_star_count' => $allReviews->whereBetween('overall_rating', [3, 3.99])->count(),
            'two_star_count' => $allReviews->whereBetween('overall_rating', [2, 2.99])->count(),
            'one_star_count' => $allReviews->where('overall_rating', '<', 2)->count(),
        ];
    }

    /**
     * Get analytics for specific questions
     */
    private function getQuestionAnalytics($doctorId)
    {
        $reviews = Review::where('doctor_id', $doctorId)
            ->published()
            ->pluck('id');

        $questionStats = ReviewAnswer::whereIn('review_id', $reviews)
            ->with('question')
            ->get()
            ->groupBy('question_id')
            ->map(function ($answers, $questionId) {
                $question = $answers->first()->question;
                
                $stats = [
                    'question' => $question->question_text,
                    'type' => $question->question_type,
                    'total_responses' => $answers->count(),
                ];

                switch ($question->question_type) {
                    case 'star_rating':
                        $stats['average_rating'] = round($answers->avg('answer_value'), 2);
                        $stats['min_rating'] = $answers->min('answer_value');
                        $stats['max_rating'] = $answers->max('answer_value');
                        break;
                    
                    case 'yes_no':
                        $yesCount = $answers->filter(function ($answer) {
                            return in_array($answer->answer_value, ['yes', '1', 1, true]);
                        })->count();
                        $stats['yes_percentage'] = $answers->count() > 0 
                            ? round(($yesCount / $answers->count()) * 100, 1) 
                            : 0;
                        $stats['yes_count'] = $yesCount;
                        $stats['no_count'] = $answers->count() - $yesCount;
                        break;
                    
                    case 'multiple_choice':
                        $stats['distribution'] = $answers->groupBy('answer_value')
                            ->map(function ($group) {
                                return $group->count();
                            })
                            ->sortDesc();
                        break;
                }

                return $stats;
            });

        return $questionStats;
    }
}
