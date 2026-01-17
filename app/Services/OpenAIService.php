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

    /**
     * Generate a patient report from session data.
     */
    public function generatePatientReport(array $sessionData, int $days = 30): array
    {
        try {
            $model = config('openai.model', self::DEFAULT_MODEL);
            
            // Prepare session context
            $sessionsContext = [];
            foreach ($sessionData as $session) {
                $sessionsContext[] = sprintf(
                    "Session: %s\nSymptoms: %s\nAlerts: %s\nTasks: %s",
                    $session['title'] ?? 'Untitled',
                    $session['symptoms'] ?? 'None',
                    $session['alerts'] ?? 'None',
                    $session['tasks'] ?? 'None'
                );
            }
            $context = implode("\n\n---\n\n", $sessionsContext);

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant helping mental health professionals analyze patient session data.

IMPORTANT DISCLAIMER: You are NOT providing medical advice or diagnoses. Your analysis is meant as a documentation aid and therapy planning tool for the treating clinician, who will make all clinical decisions.

Analyze the provided session data and generate a structured report with:
1. Summary of symptoms and patterns over the period
2. Task adherence assessment
3. Progress signals and improvements
4. Therapy planning prompts (suggestions for next session topics/questions)
5. Attention flags (non-diagnostic indicators that may need clinician attention)

Return your response as JSON with the following structure:
{
    "summary": "Brief overview",
    "symptoms_analysis": "Analysis of symptom patterns",
    "task_adherence": "Assessment of task completion",
    "progress_signals": "Notable improvements or changes",
    "therapy_prompts": ["Prompt 1", "Prompt 2"],
    "attention_flags": ["Flag 1", "Flag 2"],
    "recommendations": "General recommendations for next steps"
}

Also provide a human-readable summary text.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze the following patient session data from the last {$days} days:\n\n{$context}",
                    ],
                ],
                'max_tokens' => 2000,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content ?? null;
            if (!$content) {
                throw new \Exception('Unable to generate report.');
            }

            $jsonData = json_decode($content, true);
            if (!$jsonData) {
                throw new \Exception('Invalid response format from AI.');
            }

            // Generate human-readable summary
            $summary = $jsonData['summary'] ?? 'No summary available.';
            if (isset($jsonData['attention_flags']) && !empty($jsonData['attention_flags'])) {
                $summary .= "\n\nAttention Flags: " . implode(', ', $jsonData['attention_flags']);
            }

            return [
                'summary' => $summary,
                'json' => $jsonData,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating patient report: ' . $e->getMessage());
            throw new \Exception('Unable to generate patient report: ' . $e->getMessage());
        }
    }

    /**
     * Generate a session report.
     */
    public function generateSessionReport(array $sessionDayData): array
    {
        try {
            $model = config('openai.model', self::DEFAULT_MODEL);
            
            $context = sprintf(
                "Session: %s\nSymptoms: %s\nAlerts: %s\nTasks: %s",
                $sessionDayData['title'] ?? 'Untitled',
                $sessionDayData['symptoms'] ?? 'None',
                $sessionDayData['alerts'] ?? 'None',
                $sessionDayData['tasks'] ?? 'None'
            );

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant helping mental health professionals analyze individual session data.

IMPORTANT DISCLAIMER: You are NOT providing medical advice. Your analysis is a documentation aid.

Analyze the session data and provide:
1. Key observations
2. Notable patterns or concerns
3. Suggested follow-up questions
4. Attention flags if any

Return JSON with: summary, observations, follow_up_questions (array), attention_flags (array).',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this session data:\n\n{$context}",
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content ?? null;
            if (!$content) {
                throw new \Exception('Unable to generate report.');
            }

            $jsonData = json_decode($content, true);
            return [
                'summary' => $jsonData['summary'] ?? 'No summary available.',
                'json' => $jsonData,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating session report: ' . $e->getMessage());
            throw new \Exception('Unable to generate session report: ' . $e->getMessage());
        }
    }

    /**
     * Generate a doctor caseload report.
     */
    public function generateDoctorReport(array $patientData): array
    {
        try {
            $model = config('openai.model', self::DEFAULT_MODEL);
            
            // Prepare anonymized aggregate data
            $context = sprintf(
                "Total Patients: %d\nActive Sessions: %d\nHigh Attention Flags: %d\nPatient Distribution: %s",
                $patientData['total_patients'] ?? 0,
                $patientData['active_sessions'] ?? 0,
                $patientData['attention_flags_count'] ?? 0,
                json_encode($patientData['distribution'] ?? [])
            );

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant helping mental health professionals manage their caseload.

IMPORTANT DISCLAIMER: You are NOT providing medical advice.

Analyze the caseload data and provide:
1. Caseload overview
2. Patient distribution insights
3. High-attention flags summary
4. Follow-up reminders
5. Operational recommendations

Return JSON with: summary, overview, distribution_insights, follow_up_reminders (array), recommendations.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this doctor caseload data:\n\n{$context}",
                    ],
                ],
                'max_tokens' => 1500,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content ?? null;
            if (!$content) {
                throw new \Exception('Unable to generate report.');
            }

            $jsonData = json_decode($content, true);
            return [
                'summary' => $jsonData['summary'] ?? 'No summary available.',
                'json' => $jsonData,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating doctor report: ' . $e->getMessage());
            throw new \Exception('Unable to generate doctor report: ' . $e->getMessage());
        }
    }

    /**
     * Generate a clinic-wide report from anonymized aggregate data.
     */
    public function generateClinicReport(array $aggregateData, string $dateFrom, string $dateTo): array
    {
        try {
            $model = config('openai.model', self::DEFAULT_MODEL);
            
            // Use only anonymized aggregates - no PII
            $context = sprintf(
                "Period: %s to %s\nTotal Doctors: %d\nTotal Patients: %d\nTotal Sessions: %d\nActive Sessions: %d\nClosed Sessions: %d\nSessions Created This Period: %d\nResources Posted: %d\nTrends: %s\nAttention Flags: %d",
                $dateFrom,
                $dateTo,
                $aggregateData['total_doctors'] ?? 0,
                $aggregateData['total_patients'] ?? 0,
                $aggregateData['total_sessions'] ?? 0,
                $aggregateData['active_sessions'] ?? 0,
                $aggregateData['closed_sessions'] ?? 0,
                $aggregateData['sessions_created'] ?? 0,
                $aggregateData['resources_posted'] ?? 0,
                json_encode($aggregateData['trends'] ?? []),
                $aggregateData['attention_flags'] ?? 0
            );

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant helping clinic administrators analyze aggregate operational data.

IMPORTANT: You are analyzing anonymized aggregate data only. No patient-identifying information is included.

Analyze the clinic-wide data and provide:
1. Aggregate trends and patterns
2. Operational insights
3. Recommendations for scheduling, resource content, and operations
4. Anonymized insights (no patient names or identifiers)

Return JSON with: summary, trends_analysis, operational_insights, recommendations (array), anonymized_insights.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this clinic-wide aggregate data:\n\n{$context}",
                    ],
                ],
                'max_tokens' => 2000,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content ?? null;
            if (!$content) {
                throw new \Exception('Unable to generate report.');
            }

            $jsonData = json_decode($content, true);
            return [
                'summary' => $jsonData['summary'] ?? 'No summary available.',
                'json' => $jsonData,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating clinic report: ' . $e->getMessage());
            throw new \Exception('Unable to generate clinic report: ' . $e->getMessage());
        }
    }
}
