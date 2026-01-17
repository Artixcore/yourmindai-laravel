<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorInstruction extends Model
{
    protected $table = 'doctor_instructions';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'instruction_type',
        'title',
        'content',
        'task_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
