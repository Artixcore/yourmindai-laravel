<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientResource extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'session_id',
        'session_day_id',
        'type',
        'title',
        'file_path',
        'youtube_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor that owns the resource.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the patient that the resource belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the session that the resource is linked to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Get the session day that the resource is linked to.
     */
    public function sessionDay(): BelongsTo
    {
        return $this->belongsTo(SessionDay::class, 'session_day_id');
    }

    /**
     * Get the file download URL.
     * Uses patient route if user is a patient, otherwise uses doctor/admin route.
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        // Check if current user is a patient (User with role 'PATIENT')
        if (auth()->check() && auth()->user()->role === 'PATIENT') {
            // Use patient download route
            return route('patient.resources.download', ['resource' => $this->id]);
        }
        
        // Use doctor/admin download route
        return route('patients.resources.download', ['patient' => $this->patient_id, 'resource' => $this->id]);
    }

    /**
     * Check if resource is a PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->type === 'pdf';
    }

    /**
     * Check if resource is a YouTube video.
     */
    public function getIsYoutubeAttribute(): bool
    {
        return $this->type === 'youtube';
    }

    /**
     * Get YouTube embed URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        $videoId = $this->extractYouTubeVideoId($this->youtube_url);
        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}";
        }

        return null;
    }

    /**
     * Extract YouTube video ID from URL.
     */
    private function extractYouTubeVideoId(string $url): ?string
    {
        // Pattern 1: https://www.youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Pattern 2: https://youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Pattern 3: https://www.youtube.com/embed/VIDEO_ID
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Pattern 4: https://www.youtube.com/v/VIDEO_ID
        if (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Resolve route binding with scoped query to ensure proper access.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->where('id', $value);

        // If user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            
            // Admin can access any resource
            if ($user->role === 'admin') {
                return $query->firstOrFail();
            }
            
            // Doctor can only access resources they created
            if ($user->role === 'doctor') {
                $query->where('doctor_id', $user->id);
            }
            
            // Patient (User with role 'PATIENT') can access their own resources
            if ($user->role === 'PATIENT') {
                // Find patient by email
                $patient = \App\Models\Patient::where('email', $user->email)->first();
                if ($patient) {
                    $query->where('patient_id', $patient->id);
                } else {
                    // If no patient found, return 404
                    abort(404);
                }
            }
        }

        return $query->firstOrFail();
    }
}
