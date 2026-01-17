<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'title',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor that owns the session.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the patient that the session belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the days for the session.
     */
    public function days(): HasMany
    {
        return $this->hasMany(SessionDay::class, 'session_id');
    }

    /**
     * Resolve route binding with scoped query to ensure doctor ownership.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->where('id', $value);

        // If user is authenticated and not admin, scope by doctor ownership
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->whereHas('patient', function ($q) {
                $q->where('doctor_id', auth()->id());
            });
        }

        return $query->firstOrFail();
    }
}
