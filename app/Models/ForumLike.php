<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forum_thread_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }
}
