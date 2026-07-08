<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsQuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'lms_quiz_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'is_correct',
        'score',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'float',
    ];

    public function attempt()
    {
        return $this->belongsTo(LmsQuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(LmsQuizQuestion::class, 'question_id');
    }
}
