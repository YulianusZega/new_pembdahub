<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $table = 'report_cards';

    protected $fillable = [
        'student_id',
        'semester_id',
        'academic_year_id',
        'classroom_id',
        'average_score',
        'predicate',
        'rank',
        'total_students',
        'total_days',
        'days_present',
        'days_sick',
        'days_permission',
        'days_absent',
        'teacher_notes',
        'principal_notes',
        'achievements',
        'recommendations',
        'status',
        'finalized_by',
        'finalized_at',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'average_score' => 'float',
        'rank' => 'integer',
        'total_students' => 'integer',
        'total_days' => 'integer',
        'days_present' => 'integer',
        'days_sick' => 'integer',
        'days_permission' => 'integer',
        'days_absent' => 'integer',
        'finalized_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Relationship: ReportCard belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: ReportCard belongs to Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relationship: ReportCard belongs to AcademicYear
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relationship: ReportCard belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship: ReportCard finalized by User
     */
    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * Relationship: ReportCard published by User
     */
    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute()
    {
        if ($this->total_days == 0) {
            return 0;
        }
        return round(($this->days_present / $this->total_days) * 100, 2);
    }

    /**
     * Get predicate label with color
     */
    public function getPredicateLabelAttribute()
    {
        return match($this->predicate) {
            'A' => ['text' => 'A (Sangat Baik)', 'class' => 'success'],
            'B' => ['text' => 'B (Baik)', 'class' => 'info'],
            'C' => ['text' => 'C (Cukup)', 'class' => 'warning'],
            'D' => ['text' => 'D (Kurang)', 'class' => 'danger'],
            default => ['text' => '-', 'class' => 'secondary'],
        };
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => ['text' => 'Draft', 'class' => 'secondary'],
            'finalized' => ['text' => 'Finalized', 'class' => 'primary'],
            'published' => ['text' => 'Published', 'class' => 'success'],
            default => ['text' => 'Unknown', 'class' => 'dark'],
        };
    }

    /**
     * Check if report card is editable
     */
    public function isEditable()
    {
        return $this->status === 'draft';
    }

    /**
     * Scope: Filter by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope: Filter by semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Scope: Filter by classroom
     */
    public function scopeByClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
