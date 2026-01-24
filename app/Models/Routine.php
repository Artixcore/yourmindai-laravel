<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class Routine extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'created_by',
        'title',
        'description',
        'frequency',
        'start_time',
        'is_active',
        'custom_schedule',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'custom_schedule' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function createdByDoctor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(RoutineItem::class, 'routine_id')->orderBy('order');
    }
}
