<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLiveAnswer extends Model
{
    protected $fillable = [
        'session_id', 'player_id', 'question_index', 'answer_value', 'is_correct', 'points_earned', 'time_taken_ms'
    ];

    public function session()
    {
        return $this->belongsTo(LmsLiveSession::class, 'session_id');
    }

    public function player()
    {
        return $this->belongsTo(LmsLivePlayer::class, 'player_id');
    }
}
