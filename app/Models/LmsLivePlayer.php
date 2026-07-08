<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLivePlayer extends Model
{
    protected $fillable = [
        'session_id', 'student_id', 'nickname', 'score', 'streak', 'is_active'
    ];

    public function session()
    {
        return $this->belongsTo(LmsLiveSession::class, 'session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
