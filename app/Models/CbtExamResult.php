<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtExamResult extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_results';

    protected $fillable = [
        'exam_id', 'session_id', 'student_id',
        'total_questions', 'answered_questions', 'correct_answers', 'wrong_answers', 'unanswered',
        'total_score', 'max_score', 'percentage_score', 'final_score',
        'is_passed', 'predicate', 'rank',
        'time_spent_seconds', 'grade_synced', 'synced_grade_id',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'answered_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'unanswered' => 'integer',
        'total_score' => 'float',
        'max_score' => 'float',
        'percentage_score' => 'float',
        'final_score' => 'float',
        'is_passed' => 'boolean',
        'rank' => 'integer',
        'time_spent_seconds' => 'integer',
        'grade_synced' => 'boolean',
    ];

    public const PREDICATES = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
        'D' => 'Kurang',
    ];

    // Relationships
    public function exam(): BelongsTo { return $this->belongsTo(CbtExam::class, 'exam_id'); }
    public function session(): BelongsTo { return $this->belongsTo(CbtExamSession::class, 'session_id'); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function syncedGrade(): BelongsTo { return $this->belongsTo(Grade::class, 'synced_grade_id'); }

    // Helpers
    public static function calculatePredicate(float $score, int $kkm = 75): string
    {
        return FinalGrade::scoreToPredicate($score, $kkm);
    }

    public function getPredicateLabelAttribute(): string
    {
        return self::PREDICATES[$this->predicate] ?? '-';
    }

    // Scopes
    public function scopeByExam($query, $examId) { return $query->where('exam_id', $examId); }
    public function scopeByStudent($query, $studentId) { return $query->where('student_id', $studentId); }
    public function scopePassed($query) { return $query->where('is_passed', true); }
    public function scopeNotSynced($query) { return $query->where('grade_synced', false); }
}
