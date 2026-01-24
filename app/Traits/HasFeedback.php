<?php

namespace App\Traits;

use App\Models\Feedback;

trait HasFeedback
{
    /**
     * Get all feedback for this model.
     */
    public function feedback()
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }

    /**
     * Get feedback by source.
     */
    public function feedbackBySource(string $source)
    {
        return $this->feedback()->where('source', $source)->get();
    }

    /**
     * Get parent feedback.
     */
    public function parentFeedback()
    {
        return $this->feedback()->where('source', 'parent')->get();
    }

    /**
     * Get self feedback.
     */
    public function selfFeedback()
    {
        return $this->feedback()->where('source', 'self')->get();
    }

    /**
     * Get others feedback.
     */
    public function othersFeedback()
    {
        return $this->feedback()->where('source', 'others')->get();
    }

    /**
     * Get therapist feedback.
     */
    public function therapistFeedback()
    {
        return $this->feedback()->where('source', 'therapist')->get();
    }

    /**
     * Add feedback to this model.
     */
    public function addFeedback(int $patientId, string $source, ?int $sourceUserId = null, ?string $feedbackText = null, ?int $rating = null, ?array $customData = null)
    {
        return $this->feedback()->create([
            'patient_id' => $patientId,
            'source' => $source,
            'source_user_id' => $sourceUserId,
            'feedback_text' => $feedbackText,
            'rating' => $rating,
            'custom_data' => $customData,
        ]);
    }

    /**
     * Get average rating from feedback.
     */
    public function averageRating()
    {
        return $this->feedback()->whereNotNull('rating')->avg('rating');
    }
}
