<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsAnnouncement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_announcements';

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
