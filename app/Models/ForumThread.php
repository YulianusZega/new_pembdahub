<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'image_path',
        'attachment_path',
        'attachment_name',
        'reference_type',
        'reference_id',
        'status',
        'charity_target_amount',
        'charity_current_amount',
        'charity_target_volunteers',
        'views_count',
        'is_pinned',
        'is_locked',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'charity_target_amount' => 'decimal:2',
        'charity_current_amount' => 'decimal:2',
        'charity_target_volunteers' => 'integer',
        'views_count' => 'integer',
    ];

    public const CATEGORIES = [
        'diskusi' => '💬 Tanya & Bahas',
        'sharing' => '📂 Bagi File & Catatan',
        'info' => '📢 Info & Wara-Wara',
        'performance' => '🏆 Panggung Eksistensi',
        'art_gallery' => '🎨 Galeri Seni & Foto',
        'talent' => '🎵 Unjuk Bakat & Musik',
        'gaming' => '🎮 E-Sports & Mabar',
        'portfolio' => '🚀 Portofolio Juara',
        'project_idea' => '💡 Kolab Ide & Project',
        'committee' => '👥 Rekrut Panitia',
        'charity' => '❤️ Aksi Sosial & Donasi',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class)->orderBy('created_at', 'asc');
    }

    public function likes()
    {
        return $this->hasMany(ForumLike::class);
    }

    public function members()
    {
        return $this->hasMany(ForumMember::class);
    }

    public function approvedMembers()
    {
        return $this->hasMany(ForumMember::class)->where('status', 'approved');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    // Scopes
    public function scopePinnedFirst($query)
    {
        return $query->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
