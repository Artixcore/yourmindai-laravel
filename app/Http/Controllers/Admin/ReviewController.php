<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of all reviews
     */
    public function index(Request $request)
    {
        $query = Review::with(['patient', 'doctor', 'session', 'answers.question'])
            ->orderBy('created_at', 'desc');

        // Filter by doctor
        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by patient
        if ($request->has('patient_id') && $request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by review type
        if ($request->has('type') && in_array($request->type, ['doctor', 'session'])) {
            $query->where('review_type', $request->type);
        }

        // Filter by rating
        if ($request->has('rating') && is_numeric($request->rating)) {
            $query->where('overall_rating', '>=', $request->rating);
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'published', 'flagged'])) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $reviews = $query->paginate(20);

        // Get doctors for filter
        $doctors = User::where('role', 'doctor')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get patients for filter
        $patients = Patient::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get statistics
        $stats = [
            'total_reviews' => Review::count(),
            'published_reviews' => Review::where('status', 'published')->count(),
            'flagged_reviews' => Review::where('status', 'flagged')->count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'average_rating' => round(Review::published()->avg('overall_rating') ?? 0, 2),
        ];

        return view('admin.reviews.index', compact(
            'reviews',
            'doctors',
            'patients',
            'stats'
        ));
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $review->load([
            'patient',
            'doctor',
            'session.patient',
            'answers.question.options'
        ]);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Moderate a review (flag/unflag)
     */
    public function moderate(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:published,flagged,pending',
            'reason' => 'nullable|string|max:500',
        ]);

        $review->update([
            'status' => $request->status,
        ]);

        // You could log the moderation action here if needed
        
        // Clear doctor rating cache
        cache()->forget("doctor_{$review->doctor_id}_avg_rating");

        $message = match($request->status) {
            'flagged' => 'Review has been flagged.',
            'published' => 'Review has been published.',
            'pending' => 'Review has been marked as pending.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show review analytics
     */
    public function analytics()
    {
        // Overall statistics
        $totalReviews = Review::count();
        $averageRating = round(Review::published()->avg('overall_rating') ?? 0, 2);
        
        // Reviews by type
        $reviewsByType = Review::selectRaw('review_type, COUNT(*) as count')
            ->groupBy('review_type')
            ->pluck('count', 'review_type');

        // Reviews by status
        $reviewsByStatus = Review::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Top rated doctors
        $topRatedDoctors = User::where('role', 'doctor')
            ->withCount('doctorReviews')
            ->whereHas('doctorReviews')
            ->get()
            ->map(function ($doctor) {
                $avgRating = $doctor->doctorReviews()
                    ->where('status', 'published')
                    ->where('review_type', 'doctor')
                    ->avg('overall_rating');
                return [
                    'doctor' => $doctor,
                    'average_rating' => round($avgRating ?? 0, 2),
                    'review_count' => $doctor->doctor_reviews_count,
                ];
            })
            ->sortByDesc('average_rating')
            ->take(10);

        // Reviews over time (last 12 months)
        $reviewsOverTime = Review::where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, AVG(overall_rating) as avg_rating')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.analytics.reviews', compact(
            'totalReviews',
            'averageRating',
            'reviewsByType',
            'reviewsByStatus',
            'topRatedDoctors',
            'reviewsOverTime'
        ));
    }
}
