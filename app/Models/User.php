<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject
{
    protected $collection = 'users';

    protected $fillable = [
        'email',
        'username',
        'password_hash',
        'role',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class, 'user_id');
    }

    public function doctorPatients()
    {
        return $this->hasMany(PatientProfile::class, 'doctor_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'userId' => (string) $this->_id,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }
}
