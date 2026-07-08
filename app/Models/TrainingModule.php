<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingModule extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'content', 'pdf_file', 'thumbnail_image',
        'reading_time', 'difficulty',
        'category', 'target_roles', 'sort_order', 'is_published', 'created_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    // ── Relationships ──

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ──

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForRole($query, $role)
    {
        return $query->whereJsonContains('target_roles', $role);
    }

    // ── Accessors ──

    public function getPdfUrlAttribute()
    {
        if ($this->pdf_file) {
            return asset('storage/' . $this->pdf_file);
        }

        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_image) {
            return asset('storage/' . $this->thumbnail_image);
        }

        return null;
    }

    public function getCategoryLabelAttribute()
    {
        return match ($this->category) {
            'panduan_umum'   => 'Panduan Umum',
            'fitur_admin'    => 'Fitur Admin',
            'fitur_guru'     => 'Fitur Guru',
            'fitur_siswa'    => 'Fitur Siswa',
            'fitur_orangtua' => 'Fitur Orang Tua',
            'fitur_keuangan' => 'Fitur Keuangan',
            'fitur_yayasan'  => 'Fitur Yayasan',
            default          => ucfirst($this->category),
        };
    }

    public function getTargetRolesLabelAttribute()
    {
        $roleLabels = [
            'superadmin'    => 'Super Admin',
            'admin_sekolah' => 'Admin Sekolah',
            'guru'          => 'Guru',
            'siswa'         => 'Siswa',
            'orang_tua'     => 'Orang Tua',
            'bendahara'     => 'Bendahara',
            'ketua_yayasan' => 'Ketua Yayasan',
        ];

        if (!is_array($this->target_roles)) {
            return '';
        }

        return collect($this->target_roles)
            ->map(fn($role) => $roleLabels[$role] ?? ucfirst($role))
            ->implode(', ');
    }

    // ── Boot ──

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->title);
                $count = static::where('slug', 'like', $module->slug . '%')->count();
                if ($count > 0) {
                    $module->slug .= '-' . ($count + 1);
                }
            }
        });
    }
}
