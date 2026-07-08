<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'school_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'recorded_via',
        'device_id',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public const STATUSES = [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alpha' => 'Alpha',
        'dinas_luar' => 'Dinas Luar',
        'cuti' => 'Cuti',
    ];

    public const STATUS_COLORS = [
        'hadir' => 'green',
        'sakit' => 'yellow',
        'izin' => 'blue',
        'alpha' => 'red',
        'dinas_luar' => 'purple',
        'cuti' => 'indigo',
    ];

    // ====== RELATIONSHIPS ======

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ====== SCOPES ======

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeAbsent($query)
    {
        return $query->whereIn('status', ['sakit', 'izin', 'alpha', 'cuti']);
    }

    // ====== HELPERS ======

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    /**
     * Check if employee was late (after 07:30)
     */
    public function isLate(): bool
    {
        if (!$this->time_in || $this->status !== 'hadir') {
            return false;
        }

        // Guru tidak terpengaruh jam keterlambatan (waktu diabaikan)
        if ($this->employee?->isTeacher()) {
            return false;
        }

        $schoolClassroom = \App\Models\Classroom::where('school_id', $this->school_id)
            ->whereNotNull('entry_time')
            ->first();
        $entryTime = $schoolClassroom ? $schoolClassroom->entry_time : '07:30';

        return $this->time_in > ($entryTime . ':00');
    }

    /**
     * Get working hours (time_out - time_in)
     */
    public function getWorkingHoursAttribute(): ?float
    {
        if (!$this->time_in || !$this->time_out) {
            return null;
        }

        $in = \Carbon\Carbon::parse($this->time_in);
        $out = \Carbon\Carbon::parse($this->time_out);

        return round($out->diffInMinutes($in) / 60, 1);
    }
}
