<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionTest extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'test_name',
        'max_score',
        'weight',
        'order',
        'is_active',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'weight' => 'decimal:2',
        'order' => 'integer',
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
}
