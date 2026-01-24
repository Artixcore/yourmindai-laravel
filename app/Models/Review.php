<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'session_id',
        'review_type',
        'overall_rating',
        'comment',
        'is_anonymous',
        'status',
    ];

    protected $casts = [
        'overall_rating' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient who wrote the review
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor being reviewed
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the session being reviewed (if applicable)
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get all answers for this review
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class);
    }

    /**
     * Scope to get only published reviews
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get reviews for a specific doctor
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope to get reviews for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get reviews by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('review_type', $type);
    }

    /**
     * Get answers grouped by question
     */
    public function answersByQuestion()
    {
        return $this->answers()->with('question')->get()->groupBy('question_id');
    }

    /**
     * Check if review can be edited (within 48 hours)
     */
    public function canBeEdited(): bool
    {
        return $this->created_at->diffInHours(now()) <= 48;
    }

    /**
     * Get the display name for the patient
     */
    public function getPatientDisplayName(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous Patient';
        }
        return $this->patient->name;
    }
}
