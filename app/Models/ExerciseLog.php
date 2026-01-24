<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class ExerciseLog extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'homework_assignment_id',
        'exercise_date',
        'exercise_type',
        'duration_minutes',
        'intensity',
        'calories_burned',
        'notes',
    ];

    protected $casts = [
        'exercise_date' => 'date',
        'duration_minutes' => 'integer',
        'calories_burned' => 'integer',
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
