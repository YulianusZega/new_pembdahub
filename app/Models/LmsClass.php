<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsClass extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'lms_classes';

    protected $fillable = [
        'course_id',
        'classroom_id',
        'school_id',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected const STATUSES = [
        'active' => 'Aktif',
        'ended' => 'Berakhir',
        'archived' => 'Diarsipkan',
    ];

    /**
     * Relationship: LmsClass belongs to Course
     */
    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    /**
     * Relationship: LmsClass belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship: LmsClass belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: LmsClass has many enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(LmsEnrollment::class, 'lms_class_id');
    }

    /**
     * Get number of enrolled students
     */
    public function getEnrolledCount()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Scope: Get active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
