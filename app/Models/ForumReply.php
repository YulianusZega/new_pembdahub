<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'content',
        'is_accepted',
        'parent_reply_id',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
    ];

    // Relationships
    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_reply_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_reply_id');
    }

    public function reactions()
    {
        return $this->hasMany(ForumReaction::class);
    }

    public function getReactionCounts(): array
    {
        return $this->reactions()->select('emoji', DB::raw('count(*) as count'))
            ->groupBy('emoji')->pluck('count', 'emoji')->toArray();
    }

    public function hasReacted(User $user, string $emoji): bool
    {
        return $this->reactions()->where('user_id', $user->id)->where('emoji', $emoji)->exists();
    }
}
