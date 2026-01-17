<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiReport extends Model
{
    protected $fillable = [
        'scope',
        'patient_id',
        'session_id',
        'doctor_id',
        'requested_by',
        'date_from',
        'date_to',
        'input_snapshot_hash',
        'model',
        'status',
        'result_summary',
        'result_json',
        'error_message',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient that the report belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the session that the report belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the doctor that the report belongs to.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the user who requested the report.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the result JSON as an array.
     */
    public function getResultJsonAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set the result JSON from an array.
     */
    public function setResultJsonAttribute($value)
    {
        $this->attributes['result_json'] = $value ? json_encode($value) : null;
    }
}
