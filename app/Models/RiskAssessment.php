<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;
use App\Traits\HasPracticeProgression;

class RiskAssessment extends Model
{
    use HasFeedback, HasPracticeProgression;

    protected $fillable = [
        'patient_id',
        'homework_assignment_id',
        'assessed_by',
        'assessment_date',
        'risk_level',
        'risk_factors',
        'assessment_notes',
        'intervention_plan',
        'alert_sent',
        'alert_sent_at',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'risk_factors' => 'array',
        'alert_sent' => 'boolean',
        'alert_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function homeworkAssignment()
    {
        return $this->belongsTo(HomeworkAssignment::class, 'homework_assignment_id');
    }

    // Helper methods
    public function isHighRisk()
    {
        return in_array($this->risk_level, ['high', 'critical']);
    }

    public function needsIntervention()
    {
        return in_array($this->risk_level, ['moderate', 'high', 'critical']);
    }

    public function getRiskBadgeColor()
    {
        return match($this->risk_level) {
            'none' => 'success',
            'low' => 'info',
            'moderate' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    public function getRiskIcon()
    {
        return match($this->risk_level) {
            'none' => 'shield-check',
            'low' => 'shield',
            'moderate' => 'shield-exclamation',
            'high' => 'exclamation-triangle',
            'critical' => 'exclamation-octagon',
            default => 'shield',
        };
    }

    public function getRiskLevelLabel()
    {
        return ucfirst($this->risk_level);
    }

    // Scopes
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('assessment_date', '>=', now()->subDays($days));
    }
}
