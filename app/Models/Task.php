<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Task extends Model
{
    protected $collection = 'tasks';

    protected $fillable = [
        'patient_id',
        'title',
        'description',
        'status',
        'due_date',
        'points',
        'assigned_by_doctor_id',
        'completed_at',
        'visible_to_patient',
        'visible_to_parent',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'points' => 'integer',
        'visible_to_patient' => 'boolean',
        'visible_to_parent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function assignedByDoctor()
    {
        return $this->belongsTo(User::class, 'assigned_by_doctor_id');
    }
}
