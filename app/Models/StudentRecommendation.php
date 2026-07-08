<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRecommendation extends Model
{
    use HasFactory;

    protected $table = 'student_recommendations';

    protected $fillable = [
        'student_id',
        'school_id',
        'academic_year_id',
        'semester_id',
        'counseling_record_id',
        'recommender_role',
        'recommended_by',
        'category',
        'title',
        'description',
        'expected_outcome',
        'priority',
        'status',
        'action_result',
        'target_date',
    ];

    protected $casts = [
        'target_date' => 'date',
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

    public function counselingRecord(): BelongsTo
    {
        return $this->belongsTo(StudentCounselingRecord::class, 'counseling_record_id');
    }

    public function recommendedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }
}
