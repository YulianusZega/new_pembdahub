<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtExamQuestion extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_questions';
    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'question_id',
        'sort_order',
        'points_override',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'points_override' => 'integer',
    ];

    // Relationships
    public function exam(): BelongsTo { return $this->belongsTo(CbtExam::class, 'exam_id'); }
    public function question(): BelongsTo { return $this->belongsTo(CbtQuestion::class, 'question_id'); }

    // Helper: get effective points (override or default)
    public function getEffectivePoints(): int
    {
        return $this->points_override ?? $this->question->points ?? 1;
    }
}
