<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date',
        'time_slot',
        'status',
        'appointment_type',
        'setting_place',
        'notes',
        'reminder_enabled',
        'cancellation_reason',
    ];

    protected $casts = [
        'date' => 'datetime',
        'reminder_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
