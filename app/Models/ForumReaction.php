<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_thread_id',
        'forum_reply_id',
        'user_id',
        'emoji',
    ];

    public const EMOJIS = [
        '🔥' => 'fire',
        '❤️' => 'love',
        '😂' => 'haha',
        '🤔' => 'think',
        '💡' => 'idea',
        '👏' => 'clap'
    ];

    public static function getAlias(string $emoji): string
    {
        return self::EMOJIS[$emoji] ?? $emoji;
    }

    public static function getEmoji(string $alias): string
    {
        return array_search($alias, self::EMOJIS) ?: $alias;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function reply()
    {
        return $this->belongsTo(ForumReply::class, 'forum_reply_id');
    }
}
