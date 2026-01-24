<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class MoodLog extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'homework_assignment_id',
        'log_date',
        'log_time',
        'mood_rating',
        'mood_emoji',
        'notes',
        'triggers',
        'activities',
    ];

    protected $casts = [
        'log_date' => 'date',
        'log_time' => 'datetime',
        'triggers' => 'array',
        'activities' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function homeworkAssignment()
    {
        return $this->belongsTo(HomeworkAssignment::class, 'homework_assignment_id');
    }
}
