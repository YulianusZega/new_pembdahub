<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'basic_salary',
        'file_path',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'tetap_yayasan' => 'Tetap Yayasan',
        'honorer' => 'Honorer',
        'kontrak' => 'Kontrak',
        'pns' => 'PNS',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->contract_type] ?? $this->contract_type;
    }

    // ====== SCOPES ======

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<', now());
    }

    // ====== HELPERS ======

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date
            && $this->end_date->isFuture()
            && $this->end_date->diffInDays(now()) <= $days;
    }

    public function getDurationInMonthsAttribute(): ?int
    {
        if (!$this->end_date) return null;
        return $this->start_date->diffInMonths($this->end_date);
    }
}
