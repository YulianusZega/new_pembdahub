<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtExamParticipant extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_participants';
    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'classroom_id',
    ];

    // Relationships
    public function exam(): BelongsTo { return $this->belongsTo(CbtExam::class, 'exam_id'); }
    public function classroom(): BelongsTo { return $this->belongsTo(Classroom::class); }
}
