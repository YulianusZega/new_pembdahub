<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class KnowledgeMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'title',
        'slug',
        'description',
        'type',
        'category_type',
        'file_path',
        'external_url',
        'thumbnail_path',
        'is_public',
        'allow_download',
        'views_count',
        'likes_count',
        'bookmarks_count',
        'downloads_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'allow_download' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'bookmarks_count' => 'integer',
        'downloads_count' => 'integer',
    ];

    /**
     * Relationship: Belongs to Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relationship: Belongs to Subject
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship: Has Many Likes
     */
    public function likes(): HasMany
    {
        return $this->hasMany(KnowledgeLike::class);
    }

    /**
     * Relationship: Has Many Bookmarks
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(KnowledgeBookmark::class);
    }

    /**
     * Dynamic points calculation:
     * Upload = 10 Poin
     * Like = 2 Poin
     * Bookmark = 3 Poin
     * 10 Views = 1 Poin
     */
    public function getPointsAttribute(): int
    {
        $uploadPoints = 10;
        $likePoints = $this->likes_count * 2;
        $bookmarkPoints = $this->bookmarks_count * 3;
        $viewPoints = (int) floor($this->views_count / 10);

        return $uploadPoints + $likePoints + $bookmarkPoints + $viewPoints;
    }

    /**
     * File URL accessor
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Thumbnail URL accessor
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            return asset('storage/' . $this->thumbnail_path);
        }

        return null;
    }

    /**
     * YouTube Embed URL helper
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'video' || !$this->external_url) {
            return null;
        }

        $url = $this->external_url;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }
}
