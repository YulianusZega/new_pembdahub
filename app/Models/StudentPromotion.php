<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_classroom_id',
        'to_classroom_id',
        'academic_year_id',
        'decision',
        'average_score',
        'total_subjects',
        'passed_subjects',
        'notes',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'average_score' => 'float',
        'total_subjects' => 'integer',
        'passed_subjects' => 'integer',
        'decided_at' => 'datetime',
    ];

    public const DECISIONS = [
        'naik'   => 'Naik Kelas',
        'tinggal' => 'Tinggal Kelas',
        'lulus'  => 'Lulus',
        'pindah' => 'Pindah',
        'keluar' => 'Keluar',
    ];

    // ─── Relationships ──────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'from_classroom_id');
    }

    public function toClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'to_classroom_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function decidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function getDecisionLabelAttribute(): string
    {
        return self::DECISIONS[$this->decision] ?? ucfirst($this->decision);
    }

    public function isPromoted(): bool
    {
        return $this->decision === 'naik';
    }

    public function isGraduated(): bool
    {
        return $this->decision === 'lulus';
    }

    public function isRetained(): bool
    {
        return $this->decision === 'tinggal';
    }

    public function getPassRateAttribute(): ?float
    {
        if (!$this->total_subjects || $this->total_subjects === 0) {
            return null;
        }

        return round(($this->passed_subjects / $this->total_subjects) * 100, 1);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByAcademicYear($query, int $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeByDecision($query, string $decision)
    {
        return $query->where('decision', $decision);
    }

    public function scopePromoted($query)
    {
        return $query->where('decision', 'naik');
    }

    public function scopeGraduated($query)
    {
        return $query->where('decision', 'lulus');
    }
}
