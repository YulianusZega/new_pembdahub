<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'grades';

    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'semester_id',
        'grade_type',
        'score',
        'is_remedial',
        'created_by',
        'notes',
        'lms_source_type',
        'lms_source_id',
    ];

    protected $casts = [
        'score' => 'float',
        'is_remedial' => 'boolean',
    ];

    /**
     * Relationship: Grade belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Grade belongs to Classroom (via StudentClass)
     */
    public function classroom()
    {
        // Ambil classroom aktif dari student_classes berdasarkan student_id dan semester_id
        return $this->hasOneThrough(
            Classroom::class,
            StudentClass::class,
            'student_id', // Foreign key on student_classes
            'id',         // Local key on classrooms
            'student_id', // Local key on grades
            'classroom_id' // Foreign key on student_classes
        )->where('status', 'aktif');
    }

    /**
     * Relationship: Grade belongs to Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship: Grade belongs to Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relationship: Grade belongs to Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Check if this grade came from LMS
     */
    public function isFromLms(): bool
    {
        return !empty($this->lms_source_type);
    }

    /**
     * Get grade type display label
     */
    public function getGradeTypeLabel(): string
    {
        return match ($this->grade_type) {
            'tugas' => 'Tugas/Harian',
            'uts' => 'PTS (UTS)',
            'uas' => 'PAS (UAS)',
            'sikap' => 'Sikap',
            default => ucfirst($this->grade_type ?? '-'),
        };
    }

    /**
     * Scope: Get grades for student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Get grades by semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Scope: Exclude LMS-sourced grades
     */
    public function scopeManualOnly($query)
    {
        return $query->whereNull('lms_source_type');
    }

    /**
     * Scope: LMS-sourced grades only
     */
    public function scopeLmsOnly($query)
    {
        return $query->whereNotNull('lms_source_type');
    }
}
