<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLiveSession extends Model
{
    protected $fillable = [
        'game_id', 'host_id', 'pin_code', 'status', 'current_question_index', 'question_started_at'
    ];

    protected $casts = [
        'question_started_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(LmsGame::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function players()
    {
        return $this->hasMany(LmsLivePlayer::class, 'session_id');
    }

    public function answers()
    {
        return $this->hasMany(LmsLiveAnswer::class, 'session_id');
    }
}
