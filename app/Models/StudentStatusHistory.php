<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'student_status_histories';

    protected $fillable = [
        'student_id',
        'school_id',
        'from_status',
        'to_status',
        'reason',
        'notes',
        'document_number',
        'effective_date',
        'changed_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    /**
     * Valid student statuses with labels.
     * Unified across Student, StudentClass, and StudentStatusHistory.
     */
    public const STATUSES = [
        'calon'       => 'Calon Siswa',
        'aktif'       => 'Aktif',
        'naik'        => 'Naik Kelas',
        'tinggal'     => 'Tinggal Kelas',
        'pindah'      => 'Pindah',
        'keluar'      => 'Keluar',
        'cuti'        => 'Cuti',
        'lulus'       => 'Lulus',
        'alumni'      => 'Alumni',
        'dikeluarkan' => 'Dikeluarkan',
        'skorsing'    => 'Skorsing',
    ];

    /**
     * Statuses considered "active/enrolled" for queries.
     */
    public const ACTIVE_STATUSES = ['calon', 'aktif', 'naik'];

    /**
     * Statuses considered terminal / no longer enrolled.
     */
    public const TERMINAL_STATUSES = ['pindah', 'keluar', 'lulus', 'alumni', 'dikeluarkan'];

    /**
     * Valid status transitions.
     * Key = from_status, Value = array of allowed to_statuses.
     */
    public const TRANSITIONS = [
        'calon'       => ['aktif', 'keluar'],
        'aktif'       => ['naik', 'tinggal', 'pindah', 'keluar', 'cuti', 'lulus', 'dikeluarkan', 'skorsing'],
        'naik'        => ['aktif'],
        'tinggal'     => ['aktif'],
        'cuti'        => ['aktif', 'keluar'],
        'skorsing'    => ['aktif', 'keluar', 'dikeluarkan'],
        'lulus'       => ['alumni'],
    ];

    // ─── Relationships ──────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function getFromStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->from_status] ?? $this->from_status ?? '-';
    }

    public function getToStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->to_status] ?? $this->to_status;
    }

    /**
     * Check if a transition from $from to $to is valid.
     */
    public static function isValidTransition(?string $from, string $to): bool
    {
        if ($from === null) {
            // Initial enrollment - can only go to 'calon' or 'aktif'
            return in_array($to, ['calon', 'aktif']);
        }

        return isset(self::TRANSITIONS[$from])
            && in_array($to, self::TRANSITIONS[$from]);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('effective_date')->orderByDesc('id');
    }
}
