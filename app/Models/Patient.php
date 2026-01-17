<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    protected $fillable = [
        'doctor_id',
        'name',
        'email',
        'phone',
        'password',
        'photo_path',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor that owns the patient.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the sessions for the patient.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class, 'patient_id');
    }

    /**
     * Get the photo URL attribute.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    /**
     * Automatically hash password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        // Only hash if the value is not already a bcrypt hash
        if (!empty($value)) {
            // Check if it's already a bcrypt hash (starts with $2y$)
            if (strlen($value) === 60 && strpos($value, '$2y$') === 0) {
                // Already hashed, use as is
                $this->attributes['password'] = $value;
            } else {
                // Not hashed, hash it
                $this->attributes['password'] = Hash::make($value);
            }
        }
    }
}
