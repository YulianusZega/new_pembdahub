<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniForum extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'category',
        'title',
        'content',
        'image_path',
        'views_count',
    ];

    public const CATEGORIES = [
        'kenangan' => '🕰️ Kenangan Masa Lalu',
        'ide_kreatif' => '💡 Ide Kreatif',
        'bisnis' => '💼 Bisnis',
        'lowongan' => '📢 Lowongan Kerja',
        'acara' => '🎉 Buat Acara',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function replies()
    {
        return $this->hasMany(AlumniForumReply::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
