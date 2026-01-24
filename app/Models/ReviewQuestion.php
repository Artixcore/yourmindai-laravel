<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'question_type',
        'applies_to',
        'is_required',
        'order',
        'is_active',
        'condition_field',
        'condition_value',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the options for this question
     */
    public function options(): HasMany
    {
        return $this->hasMany(ReviewQuestionOption::class, 'question_id')->orderBy('order');
    }

    /**
     * Get all answers to this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'question_id');
    }

    /**
     * Scope to get only active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get questions for a specific review type
     */
    public function scopeForType($query, $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('applies_to', $type)
              ->orWhere('applies_to', 'both');
        });
    }

    /**
     * Scope to order questions by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Check if this question meets the condition for a given patient/session
     */
    public function meetsCondition($patient, $session = null): bool
    {
        // If no condition is set, always show the question
        if (!$this->condition_field) {
            return true;
        }

        // Check if condition field exists on patient
        if (isset($patient->{$this->condition_field})) {
            $fieldValue = $patient->{$this->condition_field};
            
            // Simple equality check
            if (strpos($this->condition_value, '>') === 0) {
                $compareValue = (int) substr($this->condition_value, 1);
                return $fieldValue > $compareValue;
            } elseif (strpos($this->condition_value, '<') === 0) {
                $compareValue = (int) substr($this->condition_value, 1);
                return $fieldValue < $compareValue;
            } else {
                return $fieldValue == $this->condition_value;
            }
        }

        // If condition field not found, don't show the question
        return false;
    }

    /**
     * Get dynamic questions for a review based on patient profile
     */
    public static function getDynamicQuestions($reviewType, $patient, $session = null)
    {
        $questions = self::active()
            ->forType($reviewType)
            ->ordered()
            ->with('options')
            ->get();

        // Filter questions based on conditions
        return $questions->filter(function ($question) use ($patient, $session) {
            return $question->meetsCondition($patient, $session);
        });
    }
}
