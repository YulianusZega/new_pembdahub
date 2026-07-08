<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsMeetingSession extends Model
{
    protected $table = 'lms_meeting_sessions';

    protected $fillable = [
        'course_id',
        'started_by',
        'started_at',
        'ended_at',
        'total_attendees',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'ended_at'    => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LmsMeetingAttendance::class, 'session_id');
    }

    // ── Accessors / Helpers ─────────────────────────────────

    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * Durasi sesi dalam menit
     */
    public function getDurationMinutesAttribute(): int
    {
        $end = $this->ended_at ?? now();
        return (int) $this->started_at->diffInMinutes($end);
    }
}
