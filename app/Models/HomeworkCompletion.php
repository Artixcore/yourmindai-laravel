<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkCompletion extends Model
{
    public const SCORING_SELF_ACTION = 'self_action';
    public const SCORING_OTHERS_HELP = 'others_help';
    public const SCORING_NOT_WORKING = 'not_working';

    public const SCORE_VALUES = [
        self::SCORING_SELF_ACTION => 10,
        self::SCORING_OTHERS_HELP => 5,
        self::SCORING_NOT_WORKING => -10,
    ];

    protected $table = 'homework_completions';

    protected $fillable = [
        'homework_assignment_id',
        'patient_id',
        'completion_date',
        'completion_time',
        'is_completed',
        'completion_percentage',
        'patient_notes',
        'completion_data',
        'scoring_choice',
        'score_value',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'is_completed' => 'boolean',
        'completion_data' => 'array',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function homeworkAssignment(): BelongsTo
    {
        return $this->belongsTo(HomeworkAssignment::class, 'homework_assignment_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get score value for a scoring choice.
     */
    public static function getScoreForChoice(?string $choice): ?int
    {
        return $choice ? (self::SCORE_VALUES[$choice] ?? null) : null;
    }

    /**
     * Set scoring choice and compute score_value.
     */
    public function setScoringChoice(?string $choice): void
    {
        $this->scoring_choice = $choice;
        $this->score_value = self::getScoreForChoice($choice);
    }
}
