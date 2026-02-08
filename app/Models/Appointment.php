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
        'session_mode',
        'booking_fee',
        'payment_status',
        'paid_at',
        'notes',
        'reminder_enabled',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'date' => 'datetime',
        'reminder_enabled' => 'boolean',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'booking_fee' => 'decimal:2',
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
