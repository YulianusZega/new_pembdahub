<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'school_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'attachment',
        'status',
        'approved_by_kepsek',
        'approved_at_kepsek',
        'approved_by_yayasan',
        'approved_at_yayasan',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'integer',
        'approved_at_kepsek' => 'datetime',
        'approved_at_yayasan' => 'datetime',
    ];

    public const LEAVE_TYPES = [
        'cuti_tahunan' => 'Cuti Tahunan',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'cuti_besar' => 'Cuti Besar',
        'dinas_luar' => 'Dinas Luar',
        'lainnya' => 'Lainnya',
    ];

    public const STATUSES = [
        'pending' => 'Menunggu Persetujuan',
        'approved_kepsek' => 'Disetujui Kepala Sekolah',
        'approved_yayasan' => 'Disetujui Yayasan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
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

    public function approvedByKepsek(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_kepsek');
    }

    public function approvedByYayasan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_yayasan');
    }

    // ====== AUTO-CALCULATE ======

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            if ($leave->start_date && $leave->end_date && !$leave->days_count) {
                $leave->days_count = $leave->start_date->diffInDays($leave->end_date) + 1;
            }
        });

        static::updating(function ($leave) {
            if ($leave->isDirty(['start_date', 'end_date'])) {
                $leave->days_count = $leave->start_date->diffInDays($leave->end_date) + 1;
            }
        });
    }

    // ====== BUSINESS LOGIC ======

    /**
     * Does this leave require Yayasan approval? (>3 days)
     */
    public function needsYayasanApproval(): bool
    {
        return $this->days_count > 3;
    }

    /**
     * Check if a user can approve this leave at the current stage
     */
    public function canBeApprovedBy(User $user): bool
    {
        // Step 1: Kepsek approves pending leaves
        if ($this->status === 'pending') {
            return $user->isSuperAdmin()
                || $user->isKetuaYayasan()
                || ($user->isAdminSekolah() && $user->school_id === $this->school_id)
                || ($user->isKepalaSekolah() && $user->school_id === $this->school_id);
        }

        // Step 2: Yayasan approves if >3 days and already approved by kepsek
        if ($this->status === 'approved_kepsek' && $this->needsYayasanApproval()) {
            return $user->isSuperAdmin() || $user->isKetuaYayasan();
        }

        return false;
    }

    /**
     * Process approval by the appropriate authority
     */
    public function approve(User $approver): void
    {
        if ($this->status === 'pending') {
            if ($this->needsYayasanApproval()) {
                // Needs 2-step: mark as approved by kepsek first
                $this->update([
                    'status' => 'approved_kepsek',
                    'approved_by_kepsek' => $approver->id,
                    'approved_at_kepsek' => now(),
                ]);
            } else {
                // ≤3 days: kepsek approval is final
                $this->update([
                    'status' => 'approved',
                    'approved_by_kepsek' => $approver->id,
                    'approved_at_kepsek' => now(),
                ]);
            }
        } elseif ($this->status === 'approved_kepsek') {
            // Yayasan final approval
            $this->update([
                'status' => 'approved',
                'approved_by_yayasan' => $approver->id,
                'approved_at_yayasan' => now(),
            ]);
        }
    }

    /**
     * Reject this leave request
     */
    public function reject(User $rejector, string $reason): void
    {
        $updateData = [
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ];

        // Track who rejected
        if ($this->status === 'pending') {
            $updateData['approved_by_kepsek'] = $rejector->id;
            $updateData['approved_at_kepsek'] = now();
        } else {
            $updateData['approved_by_yayasan'] = $rejector->id;
            $updateData['approved_at_yayasan'] = now();
        }

        $this->update($updateData);
    }

    // ====== SCOPES ======

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNeedsYayasanApproval($query)
    {
        return $query->where('status', 'approved_kepsek')
            ->where('days_count', '>', 3);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('start_date', $year);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeActiveOnDate($query, $date)
    {
        return $query->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }

    // ====== HELPERS ======

    public function getLeaveTypeLabelAttribute(): string
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? $this->leave_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved_kepsek' => 'blue',
            'approved_yayasan', 'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
