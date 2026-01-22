<?php

namespace App\Http\Controllers;

use App\Models\PsychometricScale;
use Illuminate\Http\Request;

class PsychometricScaleController extends Controller
{
    /**
     * Display a listing of psychometric scales.
     */
    public function index()
    {
        $scales = PsychometricScale::with('createdByDoctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.psychometric-scales.index', compact('scales'));
    }

    /**
     * Show the form for creating a new scale.
     */
    public function create()
    {
        return view('admin.psychometric-scales.create');
    }

    /**
     * Store a newly created scale.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:likert,scale,multiple_choice,text,textarea,number',
            'scoring_rules' => 'required|array',
            'interpretation_rules' => 'required|array|min:1',
        ]);

        // Process questions - handle options_text for multiple_choice
        $questions = collect($request->questions)->map(function($question, $index) {
            if (isset($question['options_text']) && !empty($question['options_text'])) {
                $options = array_filter(array_map('trim', explode("\n", $question['options_text'])));
                $question['options'] = array_map(function($opt, $idx) {
                    return ['text' => $opt, 'value' => $idx];
                }, $options, array_keys($options));
                unset($question['options_text']);
            }
            return $question;
        })->toArray();

        $scale = PsychometricScale::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'questions' => $questions,
            'scoring_rules' => $request->scoring_rules,
            'interpretation_rules' => $request->interpretation_rules,
            'is_active' => $request->boolean('is_active', true),
            'created_by_doctor_id' => auth()->id(),
        ]);

        return redirect()->route('psychometric-scales.index')
            ->with('success', 'Psychometric scale created successfully.');
    }

    /**
     * Display the specified scale.
     */
    public function show(PsychometricScale $psychometricScale)
    {
        $psychometricScale->load('createdByDoctor', 'assessments');
        return view('admin.psychometric-scales.show', compact('psychometricScale'));
    }

    /**
     * Show the form for editing the specified scale.
     */
    public function edit(PsychometricScale $psychometricScale)
    {
        return view('admin.psychometric-scales.edit', compact('psychometricScale'));
    }

    /**
     * Update the specified scale.
     */
    public function update(Request $request, PsychometricScale $psychometricScale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:likert,scale,multiple_choice,text,textarea,number',
            'scoring_rules' => 'required|array',
            'interpretation_rules' => 'required|array|min:1',
        ]);

        // Process questions - handle options_text for multiple_choice
        $questions = collect($request->questions)->map(function($question, $index) {
            if (isset($question['options_text']) && !empty($question['options_text'])) {
                $options = array_filter(array_map('trim', explode("\n", $question['options_text'])));
                $question['options'] = array_map(function($opt, $idx) {
                    return ['text' => $opt, 'value' => $idx];
                }, $options, array_keys($options));
                unset($question['options_text']);
            }
            return $question;
        })->toArray();

        $psychometricScale->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'questions' => $questions,
            'scoring_rules' => $request->scoring_rules,
            'interpretation_rules' => $request->interpretation_rules,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('psychometric-scales.index')
            ->with('success', 'Psychometric scale updated successfully.');
    }

    /**
     * Remove the specified scale.
     */
    public function destroy(PsychometricScale $psychometricScale)
    {
        // Check if scale has assessments
        if ($psychometricScale->assessments()->count() > 0) {
            return back()->with('error', 'Cannot delete scale with existing assessments.');
        }

        $psychometricScale->delete();

        return redirect()->route('psychometric-scales.index')
            ->with('success', 'Psychometric scale deleted successfully.');
    }
}
