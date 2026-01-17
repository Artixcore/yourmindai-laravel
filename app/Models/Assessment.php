<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Assessment extends Model
{
    protected $collection = 'assessments';

    protected $fillable = [
        'patient_id',
        'assigned_by_doctor_id',
        'assessment_type',
        'status',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function assignedByDoctor()
    {
        return $this->belongsTo(User::class, 'assigned_by_doctor_id');
    }

    public function results()
    {
        return $this->hasMany(AssessmentResult::class, 'assessment_id');
    }
}
