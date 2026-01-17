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
        // Only hash if the value is not already hashed
        if (!empty($value) && !Hash::needsRehash($value)) {
            $this->attributes['password'] = $value;
        } elseif (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}
