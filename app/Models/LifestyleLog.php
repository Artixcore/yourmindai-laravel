<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LifestyleLog extends Model
{
    protected $fillable = ['patient_id', 'type', 'label', 'value', 'logged_date'];

    protected $casts = [
        'logged_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
