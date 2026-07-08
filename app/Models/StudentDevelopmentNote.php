<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDevelopmentNote extends Model
{
    use HasFactory;

    protected $table = 'student_development_notes';

    protected $fillable = [
        'student_id',
        'school_id',
        'academic_year_id',
        'semester_id',
        'aspect',
        'observation',
        'progress',
        'challenges',
        'suggestion',
        'noted_by',
        'noted_by_role',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function notedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'noted_by');
    }
}
