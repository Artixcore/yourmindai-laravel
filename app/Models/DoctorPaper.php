<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorPaper extends Model
{
    protected $table = 'doctor_papers';

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'file_path',
        'issued_date',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the paper.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the file download URL.
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return route('doctors.papers.download', $this->id);
        }
        return null;
    }

    /**
     * Get the file extension.
     */
    public function getFileExtensionAttribute()
    {
        if ($this->file_path) {
            return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        }
        return null;
    }

    /**
     * Check if file is an image.
     */
    public function getIsImageAttribute()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        return in_array($this->file_extension, $imageExtensions);
    }

    /**
     * Check if file is a PDF.
     */
    public function getIsPdfAttribute()
    {
        return $this->file_extension === 'pdf';
    }
}
