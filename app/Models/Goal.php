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
        'visible_to_patient',
        'visible_to_parent',
    ];

    protected $casts = [
        'visible_to_patient' => 'boolean',
        'visible_to_parent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
