<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'caption', 'image', 'category',
        'sort_order', 'is_featured', 'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ── Scopes ──
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // ── Accessors ──
    public function getImageUrlAttribute()
    {
        if (\Illuminate\Support\Str::startsWith($this->image, 'images/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }

    public function getCategoryLabelAttribute()
    {
        return match ($this->category) {
            'upacara'    => 'Upacara Bendera',
            'praktikum'  => 'Praktikum Lab',
            'olahraga'   => 'Olahraga',
            'seni'       => 'Pentas Seni',
            'bengkel'    => 'Praktik Bengkel',
            'prestasi'   => 'Prestasi',
            'komputer'   => 'Lab Komputer',
            'lainnya'    => 'Lainnya',
            default      => ucfirst($this->category),
        };
    }

    public function getCategoryIconAttribute()
    {
        return match ($this->category) {
            'upacara'    => 'fa-solid fa-school',
            'praktikum'  => 'fa-solid fa-flask',
            'olahraga'   => 'fa-solid fa-futbol',
            'seni'       => 'fa-solid fa-music',
            'bengkel'    => 'fa-solid fa-wrench',
            'prestasi'   => 'fa-solid fa-trophy',
            'komputer'   => 'fa-solid fa-laptop-code',
            'lainnya'    => 'fa-solid fa-image',
            default      => 'fa-solid fa-image',
        };
    }

    public function getCategoryGradientAttribute()
    {
        return match ($this->category) {
            'upacara'    => 'linear-gradient(135deg, #0f172a, #1e3a5f)',
            'praktikum'  => 'linear-gradient(135deg, #2563eb, #60a5fa)',
            'olahraga'   => 'linear-gradient(135deg, #059669, #34d399)',
            'seni'       => 'linear-gradient(135deg, #7c3aed, #a78bfa)',
            'bengkel'    => 'linear-gradient(135deg, #d97706, #fbbf24)',
            'prestasi'   => 'linear-gradient(135deg, #dc2626, #f87171)',
            'komputer'   => 'linear-gradient(135deg, #0891b2, #22d3ee)',
            'lainnya'    => 'linear-gradient(135deg, #6b7280, #9ca3af)',
            default      => 'linear-gradient(135deg, #6b7280, #9ca3af)',
        };
    }
}
