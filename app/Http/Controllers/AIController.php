<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\ClinicalNote;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * POST /api/ai/summarize-note
     * Summarize clinical note text
     */
    public function summarizeNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        try {
            $summary = $this->openAIService->summarizeClinicalNote($request->text);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/ai/treatment-suggestions
     * Get AI-generated treatment suggestions for a patient
     */
    public function treatmentSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patientId' => 'required|string',
            'includeRecentNotes' => 'boolean',
            'notesLimit' => 'integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $user = $request->user();
        $patientId = $request->patientId;

        // Verify doctor has access to this patient
        $patient = PatientProfile::find($patientId);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'error' => "We couldn't find this patient.",
            ], 404);
        }

        if ((string) $patient->doctor_id !== (string) $user->id) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to access this patient's data.",
            ], 403);
        }

        // Build patient context
        $age = floor((now()->timestamp - $patient->date_of_birth->timestamp) / (365.25 * 24 * 60 * 60));
        $patientContext = "Patient: {$patient->full_name}, Age: {$age}, Gender: {$patient->gender}";

        // Get recent notes if requested
        $recentNotes = [];
        if ($request->input('includeRecentNotes', true)) {
            $notes = ClinicalNote::where('patient_id', $patientId)
                ->orderBy('created_at', 'desc')
                ->limit($request->input('notesLimit', 5))
                ->get();

            $recentNotes = $notes->map(function ($note) {
                $date = $note->created_at->format('Y-m-d');
                $content = $note->ai_summary ?? $note->raw_text;
                return "[{$date}] {$content}";
            })->toArray();
        }

        if (empty($recentNotes)) {
            return response()->json([
                'success' => false,
                'error' => "This patient doesn't have any session notes yet. Add your first clinical note to get personalized treatment suggestions.",
            ], 400);
        }

        try {
            $suggestions = $this->openAIService->generateTreatmentSuggestions($patientContext, $recentNotes);

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $suggestions,
                    'patientId' => $patientId,
                    'notesAnalyzed' => count($recentNotes),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
