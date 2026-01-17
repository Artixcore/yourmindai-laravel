<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class InviteCode extends Model
{
    protected $collection = 'invite_codes';

    protected $fillable = [
        'code',
        'type',
        'creator_id',
        'patient_id',
        'used',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
