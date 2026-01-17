<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionDay extends Model
{
    protected $fillable = [
        'session_id',
        'day_date',
        'symptoms',
        'alerts',
        'tasks',
    ];

    protected $casts = [
        'day_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the session that the day belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Resolve route binding with scoped query to ensure session ownership.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->where('id', $value);

        // If user is authenticated and not admin, scope by doctor ownership
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->whereHas('session.patient', function ($q) {
                $q->where('doctor_id', auth()->id());
            });
        }

        return $query->firstOrFail();
    }
}
