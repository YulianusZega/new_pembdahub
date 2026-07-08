<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionDiscount extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'discount_name',
        'discount_type',
        'discount_value',
        'applies_to',
        'duration_months',
        'criteria',
        'description',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'duration_months' => 'integer',
        'criteria' => 'json',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getDiscountLabel(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }
}
