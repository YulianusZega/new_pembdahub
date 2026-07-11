<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_poll_id',
        'forum_poll_option_id',
        'user_id',
    ];

    public function poll()
    {
        return $this->belongsTo(ForumPoll::class, 'forum_poll_id');
    }

    public function option()
    {
        return $this->belongsTo(ForumPollOption::class, 'forum_poll_option_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
