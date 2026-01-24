<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewQuestion;
use App\Models\ReviewQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewQuestionController extends Controller
{
    /**
     * Display a listing of review questions
     */
    public function index(Request $request)
    {
        $query = ReviewQuestion::with('options')->orderBy('order');

        // Filter by applies_to
        if ($request->has('applies_to') && in_array($request->applies_to, ['doctor', 'session', 'both'])) {
            $query->where('applies_to', $request->applies_to);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $questions = $query->get();

        return view('admin.review-questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new review question
     */
    public function create()
    {
        return view('admin.review-questions.create');
    }

    /**
     * Store a newly created review question
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:star_rating,yes_no,multiple_choice',
            'applies_to' => 'required|in:doctor,session,both',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'condition_field' => 'nullable|string|max:255',
            'condition_value' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.option_text' => 'required_with:options|string|max:255',
            'options.*.option_value' => 'required_with:options|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Get the next order number
            $maxOrder = ReviewQuestion::max('order') ?? 0;

            $question = ReviewQuestion::create([
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'applies_to' => $request->applies_to,
                'is_required' => $request->is_required ?? true,
                'is_active' => $request->is_active ?? true,
                'order' => $maxOrder + 1,
                'condition_field' => $request->condition_field,
                'condition_value' => $request->condition_value,
            ]);

            // Add options for multiple choice questions
            if ($request->question_type === 'multiple_choice' && $request->has('options')) {
                foreach ($request->options as $index => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'option_value' => $optionData['option_value'],
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.review-questions.index')
                ->with('success', 'Review question created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create review question.')->withInput();
        }
    }

    /**
     * Display the specified review question
     */
    public function show(ReviewQuestion $reviewQuestion)
    {
        $reviewQuestion->load('options');
        
        // Get usage statistics
        $stats = [
            'total_responses' => $reviewQuestion->answers()->count(),
            'used_in_reviews' => $reviewQuestion->answers()->distinct('review_id')->count('review_id'),
        ];

        return view('admin.review-questions.show', compact('reviewQuestion', 'stats'));
    }

    /**
     * Show the form for editing the specified review question
     */
    public function edit(ReviewQuestion $reviewQuestion)
    {
        $reviewQuestion->load('options');
        return view('admin.review-questions.edit', compact('reviewQuestion'));
    }

    /**
     * Update the specified review question
     */
    public function update(Request $request, ReviewQuestion $reviewQuestion)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:star_rating,yes_no,multiple_choice',
            'applies_to' => 'required|in:doctor,session,both',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'condition_field' => 'nullable|string|max:255',
            'condition_value' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*.option_text' => 'required_with:options|string|max:255',
            'options.*.option_value' => 'required_with:options|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $reviewQuestion->update([
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'applies_to' => $request->applies_to,
                'is_required' => $request->is_required ?? true,
                'is_active' => $request->is_active ?? true,
                'condition_field' => $request->condition_field,
                'condition_value' => $request->condition_value,
            ]);

            // Update options for multiple choice questions
            if ($request->question_type === 'multiple_choice' && $request->has('options')) {
                // Delete old options
                $reviewQuestion->options()->delete();
                
                // Add new options
                foreach ($request->options as $index => $optionData) {
                    $reviewQuestion->options()->create([
                        'option_text' => $optionData['option_text'],
                        'option_value' => $optionData['option_value'],
                        'order' => $index + 1,
                    ]);
                }
            } else {
                // If changed from multiple choice to another type, delete options
                $reviewQuestion->options()->delete();
            }

            DB::commit();

            return redirect()->route('admin.review-questions.index')
                ->with('success', 'Review question updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update review question.')->withInput();
        }
    }

    /**
     * Remove the specified review question
     */
    public function destroy(ReviewQuestion $reviewQuestion)
    {
        try {
            // Check if question has been answered
            $answerCount = $reviewQuestion->answers()->count();
            
            if ($answerCount > 0) {
                return back()->with('error', 
                    "Cannot delete this question as it has been answered {$answerCount} times. Consider deactivating it instead.");
            }

            $reviewQuestion->delete();

            return redirect()->route('admin.review-questions.index')
                ->with('success', 'Review question deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete review question.');
        }
    }

    /**
     * Reorder review questions
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:review_questions,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->questions as $order => $questionId) {
                ReviewQuestion::where('id', $questionId)->update(['order' => $order + 1]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Questions reordered successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reorder questions.'], 500);
        }
    }
}
