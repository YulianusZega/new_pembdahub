<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_poll_id',
        'option_text',
        'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function poll()
    {
        return $this->belongsTo(ForumPoll::class, 'forum_poll_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumPollVote::class, 'forum_poll_option_id');
    }

    public function percentage(): int
    {
        $total = $this->poll->totalVotes();
        if ($total === 0) {
            return 0;
        }
        return (int) round(($this->votes_count / $total) * 100);
    }
}
