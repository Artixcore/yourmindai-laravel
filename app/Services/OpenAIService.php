<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private const DEFAULT_MODEL = 'gpt-4o-mini';

    /**
     * Summarize a clinical note using OpenAI
     */
    public function summarizeClinicalNote(string $noteText): string
    {
        if (empty(trim($noteText)) || strlen(trim($noteText)) < 10) {
            throw new \Exception('Please write a bit more before generating a summary.');
        }

        try {
            $model = config('openai.model', self::DEFAULT_MODEL);

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a clinical documentation assistant helping mental health professionals summarize their session notes.

Your task is to create a concise, professional summary of the provided clinical note.

Guidelines:
- Write in neutral, clinical language
- Summarize only what is explicitly stated in the note
- Do NOT add diagnoses, interpretations, or claims not present in the original text
- Do NOT provide medical advice or treatment recommendations
- Keep the summary between 2-4 sentences
- Focus on key observations, patient statements, and session progress
- Maintain patient confidentiality in your language',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Please summarize the following clinical note:\n\n{$noteText}",
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.3,
            ]);

            $summary = $response->choices[0]->message->content ?? null;

            if (!$summary) {
                throw new \Exception('Unable to generate summary right now. Please try again.');
            }

            return trim($summary);
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());

            if ($e->getCode() === 401) {
                throw new \Exception('AI service configuration error. Please contact support.');
            }
            if ($e->getCode() === 429) {
                throw new \Exception('AI service is busy right now. Please wait a moment and try again.');
            }
            if ($e->getCode() === 500) {
                throw new \Exception('AI service is temporarily unavailable. Please try again later.');
            }

            throw new \Exception('Something went wrong while generating the summary. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unknown error during summarization: ' . $e->getMessage());
            throw new \Exception('Unable to summarize this note right now. Please try again.');
        }
    }

    /**
     * Generate treatment recommendations based on clinical notes
     */
    public function generateTreatmentSuggestions(string $patientContext, array $recentNotes): string
    {
        try {
            $model = config('openai.model', self::DEFAULT_MODEL);
            $notesContext = implode("\n\n---\n\n", $recentNotes);

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant helping mental health professionals with documentation.

IMPORTANT DISCLAIMER: You are NOT providing medical advice. Your suggestions are meant as documentation aids and conversation starters for the treating clinician, who will make all clinical decisions.

Based on the provided patient context and recent clinical notes, suggest potential areas for the clinician to consider in treatment planning. Frame everything as "consider" or "may want to explore" rather than definitive recommendations.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Patient Context: {$patientContext}\n\nRecent Session Notes:\n{$notesContext}\n\nPlease provide potential areas for the clinician to consider in upcoming sessions.",
                    ],
                ],
                'max_tokens' => 800,
                'temperature' => 0.5,
            ]);

            return $response->choices[0]->message->content ?? 'Unable to generate suggestions.';
        } catch (\Exception $e) {
            Log::error('Error generating treatment suggestions: ' . $e->getMessage());
            throw new \Exception('Unable to generate suggestions right now. Please try again.');
        }
    }
}
