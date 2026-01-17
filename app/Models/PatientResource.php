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
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return route('patients.resources.download', ['patient' => $this->patient_id, 'resource' => $this->id]);
        }
        return null;
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
     * Resolve route binding with scoped query to ensure doctor ownership.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->where('id', $value);

        // If user is authenticated and not admin, scope by doctor ownership
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }

        return $query->firstOrFail();
    }
}
