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
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:likert,scale,multiple_choice,text,textarea,number',
            'scoring_rules' => 'nullable|array',
            'interpretation_rules' => 'required|array|min:1',
            'interpretation_rules.*.min' => 'required|numeric',
            'interpretation_rules.*.max' => 'required|numeric',
            'interpretation_rules.*.interpretation' => 'required|string',
        ]);

        // Process questions - handle options_text for multiple_choice and auto-generate IDs
        $questions = collect($request->questions)->map(function($question, $index) {
            // Auto-generate ID if not provided
            if (!isset($question['id'])) {
                $question['id'] = $index;
            }
            
            // Handle options_text for multiple_choice
            if (isset($question['options_text']) && !empty($question['options_text'])) {
                $options = array_filter(array_map('trim', explode("\n", $question['options_text'])));
                $question['options'] = array_map(function($opt, $idx) {
                    return ['text' => $opt, 'value' => $idx];
                }, $options, array_keys($options));
                unset($question['options_text']);
            }
            return $question;
        })->toArray();

        // Process scoring rules - set default if not provided
        $scoringRules = $request->scoring_rules ?? [];
        if (empty($scoringRules) || !isset($scoringRules['type'])) {
            $scoringRules = ['type' => 'sum'];
        }

        // Process interpretation rules
        $interpretationRules = collect($request->interpretation_rules)->map(function($rule) {
            return [
                'min' => (int) $rule['min'],
                'max' => (int) $rule['max'],
                'interpretation' => $rule['interpretation'],
            ];
        })->toArray();

        $scale = PsychometricScale::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'questions' => $questions,
            'scoring_rules' => $scoringRules,
            'interpretation_rules' => $interpretationRules,
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
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:likert,scale,multiple_choice,text,textarea,number',
            'scoring_rules' => 'nullable|array',
            'interpretation_rules' => 'required|array|min:1',
            'interpretation_rules.*.min' => 'required|numeric',
            'interpretation_rules.*.max' => 'required|numeric',
            'interpretation_rules.*.interpretation' => 'required|string',
        ]);

        // Process questions - handle options_text for multiple_choice and auto-generate IDs
        $questions = collect($request->questions)->map(function($question, $index) {
            // Auto-generate ID if not provided
            if (!isset($question['id'])) {
                $question['id'] = $index;
            }
            
            // Handle options_text for multiple_choice
            if (isset($question['options_text']) && !empty($question['options_text'])) {
                $options = array_filter(array_map('trim', explode("\n", $question['options_text'])));
                $question['options'] = array_map(function($opt, $idx) {
                    return ['text' => $opt, 'value' => $idx];
                }, $options, array_keys($options));
                unset($question['options_text']);
            }
            return $question;
        })->toArray();

        // Process scoring rules - set default if not provided
        $scoringRules = $request->scoring_rules ?? [];
        if (empty($scoringRules) || !isset($scoringRules['type'])) {
            $scoringRules = ['type' => 'sum'];
        }

        // Process interpretation rules
        $interpretationRules = collect($request->interpretation_rules)->map(function($rule) {
            return [
                'min' => (int) $rule['min'],
                'max' => (int) $rule['max'],
                'interpretation' => $rule['interpretation'],
            ];
        })->toArray();

        $psychometricScale->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'questions' => $questions,
            'scoring_rules' => $scoringRules,
            'interpretation_rules' => $interpretationRules,
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
