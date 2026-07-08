<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'lms_quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'finished_at',
        'score',
        'is_passed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'score' => 'float',
        'is_passed' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(LmsQuizAnswer::class, 'attempt_id');
    }

    public function isFinished()
    {
        return $this->finished_at !== null;
    }

    public function getDurationAttribute()
    {
        if (!$this->finished_at) return null;
        return $this->started_at->diffInMinutes($this->finished_at);
    }
}
