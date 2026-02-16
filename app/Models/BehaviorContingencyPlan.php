<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BehaviorContingencyPlan extends Model
{
    protected $table = 'behavior_contingency_plans';

    protected $fillable = [
        'patient_id',
        'patient_profile_id',
        'created_by',
        'title',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BehaviorContingencyPlanItem::class, 'plan_id')->orderBy('sort_order');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(BehaviorContingencyCheckin::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getUser()
    {
        if ($this->patient_profile_id && $this->patientProfile) {
            return $this->patientProfile->user;
        }
        if ($this->patient_id && $this->patient) {
            return \App\Models\User::where('email', $this->patient->email)->first();
        }
        return null;
    }
}
