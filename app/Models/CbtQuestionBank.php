<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtQuestionBank extends Model
{
    use HasFactory;

    protected $table = 'cbt_question_banks';

    protected $fillable = [
        'school_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'bank_name',
        'description',
        'grade_level',
        'total_questions',
        'is_active',
        'is_shared',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
        'total_questions' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CbtQuestion::class, 'question_bank_id');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(CbtExam::class, 'cbt_exam_question_bank', 'question_bank_id', 'exam_id')
            ->withPivot('questions_to_pick');
    }
}
