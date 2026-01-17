<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'username',
        'password',
        'password_hash',
        'role',
        'phone',
        'address',
        'avatar',
        'avatar_path',
        'status',
    ];

    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
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

    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    public function papers()
    {
        return $this->hasMany(DoctorPaper::class, 'user_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'doctor_id');
    }

    /**
     * Get the assistant assignments for this user (if they are an assistant).
     */
    public function assistantAssignments()
    {
        return $this->hasMany(AssistantDoctorAssignment::class, 'assistant_id');
    }

    /**
     * Get the doctor assignments for this user (if they are a doctor).
     */
    public function doctorAssignments()
    {
        return $this->hasMany(AssistantDoctorAssignment::class, 'doctor_id');
    }

    /**
     * Get the assigned doctors for this assistant.
     */
    public function assignedDoctors()
    {
        return $this->belongsToMany(User::class, 'assistant_doctor_assignments', 'assistant_id', 'doctor_id');
    }

    /**
     * Get the assigned assistants for this doctor.
     */
    public function assignedAssistants()
    {
        return $this->belongsToMany(User::class, 'assistant_doctor_assignments', 'doctor_id', 'assistant_id');
    }

    /**
     * Get the AI reports requested by this user.
     */
    public function requestedReports()
    {
        return $this->hasMany(AiReport::class, 'requested_by');
    }

    /**
     * Get the AI reports for this doctor.
     */
    public function doctorReports()
    {
        return $this->hasMany(AiReport::class, 'doctor_id');
    }

    /**
     * Get the audit logs created by this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is doctor.
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is assistant.
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        return null;
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
            'userId' => (string) $this->id,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash ?? $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
