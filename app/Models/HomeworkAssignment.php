<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class HomeworkAssignment extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'assigned_by',
        'session_id',
        'homework_type',
        'title',
        'description',
        'instructions',
        'goals',
        'frequency',
        'start_date',
        'end_date',
        'status',
        'requires_parent_feedback',
        'requires_others_feedback',
        'custom_fields',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'goals' => 'array',
        'custom_fields' => 'array',
        'requires_parent_feedback' => 'boolean',
        'requires_others_feedback' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient this homework is assigned to.
     */
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Get the doctor who assigned this homework.
     */
    public function assignedByDoctor()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the session this homework is associated with.
     */
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Get completions for this homework.
     */
    public function completions()
    {
        return $this->hasMany(HomeworkCompletion::class, 'homework_assignment_id');
    }

    /**
     * Get mood logs if this is mood tracking homework.
     */
    public function moodLogs()
    {
        return $this->hasMany(MoodLog::class, 'homework_assignment_id');
    }

    /**
     * Get sleep logs if this is sleep tracking homework.
     */
    public function sleepLogs()
    {
        return $this->hasMany(SleepLog::class, 'homework_assignment_id');
    }

    /**
     * Get exercise logs if this is exercise homework.
     */
    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class, 'homework_assignment_id');
    }
}
