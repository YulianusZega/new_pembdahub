<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumni_forum_id',
        'user_id',
        'content',
    ];

    public function forum()
    {
        return $this->belongsTo(AlumniForum::class, 'alumni_forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
