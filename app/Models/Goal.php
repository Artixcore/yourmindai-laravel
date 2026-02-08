<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'goals';

    protected $fillable = [
        'patient_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'frequency_per_day',
        'duration_minutes',
        'visible_to_patient',
        'visible_to_parent',
    ];

    protected $casts = [
        'visible_to_patient' => 'boolean',
        'visible_to_parent' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
