<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsEnrollment extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'lms_enrollments';

    protected $fillable = [
        'lms_class_id',
        'student_id',
        'status',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected const STATUSES = [
        'enrolled' => 'Terdaftar',
        'in_progress' => 'Sedang Belajar',
        'completed' => 'Selesai',
        'dropped' => 'Keluar',
    ];

    /**
     * Relationship: Enrollment belongs to LMS Class
     */
    public function lmsClass()
    {
        return $this->belongsTo(LmsClass::class, 'lms_class_id');
    }

    /**
     * Relationship: Enrollment belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Scope: Get active enrollments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'in_progress']);
    }

    /**
     * Scope: Get completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
