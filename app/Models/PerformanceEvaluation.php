<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'performance_contract_id',
        'semester_id',
        'evaluated_by',
        'evaluation_data',
        'score',
        'status',
        'notes',
    ];

    protected $casts = [
        'evaluation_data' => 'array',
        'score' => 'decimal:2',
    ];

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED_TO_YAYASAN = 'submitted_to_yayasan';
    public const STATUS_APPROVED_BY_YAYASAN = 'approved_by_yayasan';
    public const STATUS_REJECTED = 'rejected';

    public function contract(): BelongsTo
    {
        return $this->belongsTo(PerformanceContract::class, 'performance_contract_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
