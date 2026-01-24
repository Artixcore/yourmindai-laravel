<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class SleepLog extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'homework_assignment_id',
        'sleep_date',
        'bedtime',
        'wake_time',
        'hours_slept',
        'sleep_quality',
        'times_woken',
        'notes',
        'factors',
    ];

    protected $casts = [
        'sleep_date' => 'date',
        'factors' => 'array',
        'bedtime' => 'datetime',
        'wake_time' => 'datetime',
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
