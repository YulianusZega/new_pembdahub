<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_thread_id',
        'question',
        'is_multiple_choice',
        'closes_at',
    ];

    protected $casts = [
        'is_multiple_choice' => 'boolean',
        'closes_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function options()
    {
        return $this->hasMany(ForumPollOption::class, 'forum_poll_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumPollVote::class, 'forum_poll_id');
    }

    public function isOpen(): bool
    {
        return is_null($this->closes_at) || $this->closes_at->isFuture();
    }

    public function totalVotes(): int
    {
        return $this->options()->sum('votes_count');
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
