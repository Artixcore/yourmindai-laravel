<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;

class GeneralAssessment extends Model
{
    use HasFeedback;

    protected $fillable = [
        'patient_id',
        'assigned_by',
        'title',
        'description',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'results',
        'therapist_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'results' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient this assessment is for.
     */
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Get the doctor who assigned this assessment.
     */
    public function assignedByDoctor()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the questions for this assessment.
     */
    public function questions()
    {
        return $this->hasMany(GeneralAssessmentQuestion::class, 'assessment_id')->orderBy('order');
    }

    /**
     * Get the responses for this assessment.
     */
    public function responses()
    {
        return $this->hasMany(GeneralAssessmentResponse::class, 'assessment_id');
    }

    /**
     * Check if assessment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if assessment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
