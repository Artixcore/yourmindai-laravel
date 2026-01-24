<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'patient_id',
        'referred_by',
        'referred_to',
        'referral_type',
        'original_referral_id',
        'specialty_needed',
        'reason',
        'patient_history_summary',
        'recommendations',
        'report_file_path',
        'attached_documents',
        'status',
        'response_notes',
        'referred_at',
        'responded_at',
        'completed_at',
    ];

    protected $casts = [
        'attached_documents' => 'array',
        'referred_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredTo()
    {
        return $this->belongsTo(User::class, 'referred_to');
    }

    public function originalReferral()
    {
        return $this->belongsTo(Referral::class, 'original_referral_id');
    }

    public function backReferrals()
    {
        return $this->hasMany(Referral::class, 'original_referral_id');
    }

    // Helper methods
    public function isForwardReferral()
    {
        return $this->referral_type === 'forward';
    }

    public function isBackReferral()
    {
        return $this->referral_type === 'back';
    }

    public function canRespond(User $user): bool
    {
        // Only the referred-to user can respond
        return $user->id === $this->referred_to;
    }

    public function accept($responseNotes = null)
    {
        $this->update([
            'status' => 'accepted',
            'response_notes' => $responseNotes,
            'responded_at' => now(),
        ]);
    }

    public function decline($responseNotes = null)
    {
        $this->update([
            'status' => 'declined',
            'response_notes' => $responseNotes,
            'responded_at' => now(),
        ]);
    }

    public function updateProgress($status, $responseNotes = null)
    {
        $this->update([
            'status' => $status,
            'response_notes' => $responseNotes,
        ]);
    }

    public function complete($responseNotes = null)
    {
        $this->update([
            'status' => 'completed',
            'response_notes' => $responseNotes,
            'completed_at' => now(),
        ]);
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'declined' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'pending' => 'clock',
            'accepted' => 'check-circle',
            'in_progress' => 'arrow-repeat',
            'completed' => 'check-circle-fill',
            'declined' => 'x-circle',
            default => 'circle',
        };
    }

    public function getStatusLabel()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getTypeLabel()
    {
        return $this->referral_type === 'forward' ? 'Forward Referral' : 'Back Referral';
    }

    public function getTypeIcon()
    {
        return $this->referral_type === 'forward' ? 'arrow-right-circle' : 'arrow-left-circle';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'in_progress']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('referred_by', $userId)
              ->orWhere('referred_to', $userId);
        });
    }

    public function scopeForwardReferrals($query)
    {
        return $query->where('referral_type', 'forward');
    }

    public function scopeBackReferrals($query)
    {
        return $query->where('referral_type', 'back');
    }
}
