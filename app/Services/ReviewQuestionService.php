<?php

namespace App\Services;

use App\Models\ReviewQuestion;
use App\Models\Patient;
use App\Models\Session;

class ReviewQuestionService
{
    /**
     * Get questions for a review based on review type and patient context
     *
     * @param string $reviewType 'doctor' or 'session'
     * @param Patient $patient
     * @param Session|null $session
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuestionsForReview(string $reviewType, Patient $patient, ?Session $session = null)
    {
        // Get all active questions for the review type
        $questions = ReviewQuestion::active()
            ->forType($reviewType)
            ->ordered()
            ->with('options')
            ->get();

        // Filter questions based on patient profile conditions
        return $questions->filter(function ($question) use ($patient, $session) {
            return $this->meetsCondition($question, $patient, $session);
        });
    }

    /**
     * Check if a question meets its condition for the given patient/session
     *
     * @param ReviewQuestion $question
     * @param Patient $patient
     * @param Session|null $session
     * @return bool
     */
    private function meetsCondition(ReviewQuestion $question, Patient $patient, ?Session $session): bool
    {
        // If no condition is set, always show the question
        if (!$question->condition_field) {
            return true;
        }

        // Try to get the field value from patient first
        $fieldValue = $this->getFieldValue($question->condition_field, $patient, $session);

        if ($fieldValue === null) {
            // If condition field doesn't exist, don't show the question
            return false;
        }

        // Parse and evaluate the condition
        return $this->evaluateCondition($fieldValue, $question->condition_value);
    }

    /**
     * Get field value from patient or session
     *
     * @param string $fieldName
     * @param Patient $patient
     * @param Session|null $session
     * @return mixed
     */
    private function getFieldValue(string $fieldName, Patient $patient, ?Session $session)
    {
        // Special computed fields
        if ($fieldName === 'session_count') {
            return $patient->sessions()->count();
        }

        if ($fieldName === 'completed_session_count') {
            return $patient->sessions()->where('status', 'closed')->count();
        }

        if ($fieldName === 'has_assessments') {
            return $patient->psychometricAssessments()->count() > 0;
        }

        // Check patient model fields
        if (isset($patient->{$fieldName})) {
            return $patient->{$fieldName};
        }

        // Check session fields if session is provided
        if ($session && isset($session->{$fieldName})) {
            return $session->{$fieldName};
        }

        return null;
    }

    /**
     * Evaluate a condition
     *
     * @param mixed $fieldValue
     * @param string $conditionValue
     * @return bool
     */
    private function evaluateCondition($fieldValue, string $conditionValue): bool
    {
        // Greater than comparison
        if (strpos($conditionValue, '>') === 0) {
            $compareValue = (int) substr($conditionValue, 1);
            return (int) $fieldValue > $compareValue;
        }

        // Less than comparison
        if (strpos($conditionValue, '<') === 0) {
            $compareValue = (int) substr($conditionValue, 1);
            return (int) $fieldValue < $compareValue;
        }

        // Greater than or equal
        if (strpos($conditionValue, '>=') === 0) {
            $compareValue = (int) substr($conditionValue, 2);
            return (int) $fieldValue >= $compareValue;
        }

        // Less than or equal
        if (strpos($conditionValue, '<=') === 0) {
            $compareValue = (int) substr($conditionValue, 2);
            return (int) $fieldValue <= $compareValue;
        }

        // Not equal
        if (strpos($conditionValue, '!=') === 0) {
            $compareValue = substr($conditionValue, 2);
            return $fieldValue != $compareValue;
        }

        // Default: equality comparison
        return $fieldValue == $conditionValue;
    }

    /**
     * Validate review answers against questions
     *
     * @param array $answers
     * @param \Illuminate\Database\Eloquent\Collection $questions
     * @return array Validation errors
     */
    public function validateAnswers(array $answers, $questions): array
    {
        $errors = [];

        foreach ($questions as $question) {
            $questionId = $question->id;
            $answer = $answers[$questionId] ?? null;

            // Check required questions
            if ($question->is_required && empty($answer)) {
                $errors["question_{$questionId}"] = "This question is required.";
                continue;
            }

            // Validate star ratings
            if ($question->question_type === 'star_rating' && $answer) {
                if (!is_numeric($answer) || $answer < 1 || $answer > 5) {
                    $errors["question_{$questionId}"] = "Rating must be between 1 and 5.";
                }
            }

            // Validate yes/no questions
            if ($question->question_type === 'yes_no' && $answer) {
                if (!in_array($answer, ['yes', 'no', '1', '0'])) {
                    $errors["question_{$questionId}"] = "Invalid yes/no answer.";
                }
            }

            // Validate multiple choice
            if ($question->question_type === 'multiple_choice' && $answer) {
                $validOptions = $question->options->pluck('option_value')->toArray();
                if (!in_array($answer, $validOptions)) {
                    $errors["question_{$questionId}"] = "Invalid option selected.";
                }
            }
        }

        return $errors;
    }
}
