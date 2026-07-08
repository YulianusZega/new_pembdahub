<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'schedules';

    protected $fillable = [
        'school_id',
        'teaching_assignment_id',
        'classroom_id',
        'subject_id',
        'teacher_id',
        'semester_id',
        'academic_year_id',
        'semester',
        'time_slot_id',
        'duration_slots',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'group_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_slots' => 'integer',
    ];

    protected const DAYS = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
    ];

    /**
     * Relationship: Schedule belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: Schedule belongs to Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relationship: Schedule belongs to Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship: Schedule belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship: Schedule belongs to TimeSlot
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Relationship: Schedule belongs to Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relationship: Schedule belongs to Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relationship: Schedule belongs to TeachingAssignment (parent assignment)
     */
    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    /**
     * Get day label
     */
    public function getDayLabel()
    {
        return self::DAYS[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Scope: Get active schedules only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by day
     */
    public function scopeByDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Scope: Filter by teacher
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope: Filter by classroom
     */
    public function scopeByClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }
}
