<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'academic_year_id',
        'school_id',
        'contract_type',
        'position_id',
        'target_data',
        'status',
        'notes',
    ];

    protected $casts = [
        'target_data' => 'array',
    ];

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED_TO_KEPSEK = 'submitted_to_kepsek';
    public const STATUS_APPROVED_BY_KEPSEK = 'approved_by_kepsek';
    public const STATUS_APPROVED_BY_YAYASAN = 'approved_by_yayasan';
    public const STATUS_REJECTED = 'rejected';

    // Type Constants
    public const TYPE_PKG_KEJURUAN = 'pkg_kejuruan';
    public const TYPE_PKG_UMUM = 'pkg_umum';
    public const TYPE_JABATAN = 'jabatan_tambahan';

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function evaluations()
    {
        return $this->hasMany(PerformanceEvaluation::class);
    }

    // Scopes
    public function scopeApprovedByYayasan($query)
    {
        return $query->where('status', self::STATUS_APPROVED_BY_YAYASAN);
    }
}
