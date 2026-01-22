<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychometricScale extends Model
{
    protected $table = 'psychometric_scales';

    protected $fillable = [
        'name',
        'description',
        'category',
        'questions',
        'scoring_rules',
        'interpretation_rules',
        'is_active',
        'created_by_doctor_id',
    ];

    protected $casts = [
        'questions' => 'array',
        'scoring_rules' => 'array',
        'interpretation_rules' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor who created this scale.
     */
    public function createdByDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_doctor_id');
    }

    /**
     * Get all assessments using this scale.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(PsychometricAssessment::class, 'scale_id');
    }

    /**
     * Calculate score based on responses.
     */
    public function calculateScore(array $responses): int
    {
        $scoringRules = $this->scoring_rules ?? [];
        $totalScore = 0;

        foreach ($responses as $questionId => $response) {
            $question = collect($this->questions)->firstWhere('id', $questionId);
            if (!$question) {
                continue;
            }

            $score = 0;
            if (isset($scoringRules['type'])) {
                switch ($scoringRules['type']) {
                    case 'sum':
                        // Simple sum of response values
                        $score = is_numeric($response) ? (int)$response : 0;
                        break;
                    case 'weighted':
                        // Weighted scoring
                        $weight = $scoringRules['weights'][$questionId] ?? 1;
                        $score = (is_numeric($response) ? (int)$response : 0) * $weight;
                        break;
                    default:
                        $score = is_numeric($response) ? (int)$response : 0;
                }
            } else {
                // Default: sum of numeric responses
                $score = is_numeric($response) ? (int)$response : 0;
            }

            $totalScore += $score;
        }

        return $totalScore;
    }

    /**
     * Interpret score based on interpretation rules.
     */
    public function interpretScore(int $score): string
    {
        $interpretationRules = $this->interpretation_rules ?? [];
        
        if (empty($interpretationRules)) {
            return "Score: {$score}";
        }

        foreach ($interpretationRules as $rule) {
            $min = $rule['min'] ?? 0;
            $max = $rule['max'] ?? PHP_INT_MAX;
            
            if ($score >= $min && $score <= $max) {
                return $rule['interpretation'] ?? "Score: {$score}";
            }
        }

        return "Score: {$score}";
    }
}
