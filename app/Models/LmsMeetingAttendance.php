<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsMeetingAttendance extends Model
{
    protected $table = 'lms_meeting_attendances';

    protected $fillable = [
        'session_id',
        'course_id',
        'student_id',
        'joined_at',
        'left_at',
        'duration_seconds',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at'   => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────

    public function session(): BelongsTo
    {
        return $this->belongsTo(LmsMeetingSession::class, 'session_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // ── Helpers ─────────────────────────────────────────────

    /**
     * Durasi kehadiran dalam menit (formatted)
     */
    public function getDurationLabelAttribute(): string
    {
        $seconds = $this->duration_seconds;
        if ($seconds === 0 && is_null($this->left_at)) {
            // Masih aktif (defensif terhadap clock skew)
            $seconds = max(0, now()->timestamp - $this->joined_at->timestamp);
        }
        $minutes = intdiv($seconds, 60);
        $secs    = $seconds % 60;
        return "{$minutes}m {$secs}s";
    }

    public function isStillPresent(): bool
    {
        return is_null($this->left_at);
    }

    /**
     * Finalize durasi saat siswa keluar
     */
    public function recordLeave(): void
    {
        if (is_null($this->left_at)) {
            $now = now();
            // Cegah anomali left_at lebih kecil dari joined_at akibat clock skew antara Web Server & DB Server
            $leftAt = $now->isBefore($this->joined_at) ? $this->joined_at : $now;
            $duration = max(0, $leftAt->timestamp - $this->joined_at->timestamp);
            
            $this->update([
                'left_at'          => $leftAt,
                'duration_seconds' => (int)$duration,
            ]);
        }
    }
}
