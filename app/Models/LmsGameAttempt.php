<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsGameAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'student_id',
        'status',
        'score',
    ];

    public function game()
    {
        return $this->belongsTo(LmsGame::class, 'game_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
