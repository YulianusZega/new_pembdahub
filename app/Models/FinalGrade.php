<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    use HasFactory;

    protected $table = 'final_grades';

    protected $fillable = [
        'student_id',
        'subject_id',
        'semester_id',
        'teacher_id',
        'tugas_score',
        'pts_score',
        'pas_score',
        'sikap_score',
        'final_score',
        'kkm',
        'is_passed',
        'predicate',
        'description',
    ];

    protected $casts = [
        'tugas_score' => 'float',
        'pts_score' => 'float',
        'pas_score' => 'float',
        'sikap_score' => 'float',
        'final_score' => 'float',
        'kkm' => 'integer',
        'is_passed' => 'boolean',
    ];

    /**
     * Predicate thresholds
     */
    const PREDICATES = [
        'A' => 90,
        'B' => 80,
        'C' => 70,
        'D' => 0,
    ];

    /**
     * Relationship: FinalGrade belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: FinalGrade belongs to Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship: FinalGrade belongs to Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relationship: FinalGrade belongs to Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get predicate from score based on KKM or static thresholds (configured per grade level)
     */
    public static function scoreToPredicate(float $score, int $kkm = 75, ?int $gradeLevel = null): string
    {
        if ($gradeLevel !== null) {
            $conversionSettings = Setting::getValue('raport_grade_conversion', []);
            $levelSettings = $conversionSettings[$gradeLevel] ?? null;
            
            if ($levelSettings && ($levelSettings['mode'] ?? 'kkm_interval') === 'static') {
                $a = $levelSettings['static_a'] ?? 90;
                $b = $levelSettings['static_b'] ?? 80;
                $c = $levelSettings['static_c'] ?? 70;
                
                return match (true) {
                    $score >= $a => 'A',
                    $score >= $b => 'B',
                    $score >= $c => 'C',
                    default => 'D',
                };
            }
        }

        // Fallback: KKM-based dynamic interval
        $kkm = $kkm > 0 ? $kkm : 75;
        $interval = (100 - $kkm) / 3;
        
        return match (true) {
            $score >= (100 - $interval) => 'A',
            $score >= (100 - 2 * $interval) => 'B',
            $score >= $kkm => 'C',
            default => 'D',
        };
    }

    /**
     * Get predicate description
     */
    public function getPredicateDescription(): string
    {
        return match ($this->predicate) {
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            default => '-',
        };
    }

    /**
     * Scope: Get final grades for semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Scope: Get passed grades only
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope: Get failed grades
     */
    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    /**
     * Scope: By student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
