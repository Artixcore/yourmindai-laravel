<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceAction extends Model
{
    protected $fillable = [
        'patient_profile_id',
        'patient_id',
        'device_id',
        'action_type',
        'action_note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function actionTypes(): array
    {
        return [
            'connected_smartwatch' => 'Connected smartwatch',
            'logged_screentime' => 'Logged screentime',
            'tracked_sleep' => 'Tracked sleep',
            'synced_steps' => 'Synced steps',
            'manual_entry' => 'Manual entry',
            'other' => 'Other',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(PatientDevice::class, 'device_id');
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
