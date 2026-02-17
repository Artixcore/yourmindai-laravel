<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorLink extends Model
{
    protected $table = 'supervisor_links';

    protected $fillable = [
        'supervisor_id',
        'patient_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
