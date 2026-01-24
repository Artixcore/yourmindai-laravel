<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'feedbackable_type',
        'feedbackable_id',
        'patient_id',
        'source',
        'source_user_id',
        'feedback_text',
        'rating',
        'custom_data',
        'feedback_date',
    ];

    protected $casts = [
        'custom_data' => 'array',
        'feedback_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent feedbackable model (polymorphic).
     */
    public function feedbackable()
    {
        return $this->morphTo();
    }

    /**
     * Get the patient this feedback is for.
     */
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Get the user who provided the feedback.
     */
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }
}
