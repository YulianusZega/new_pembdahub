<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Semester extends Model
{
    use HasFactory;

    // migration does not include timestamps
    public $timestamps = false;

    protected $fillable = [
        'academic_year_id',
        'semester_number',
        'semester_name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const CACHE_KEY_ACTIVE = 'semester.active';
    const CACHE_TTL = 3600; // 1 jam

    /**
     * Get active semester (cached)
     */
    public static function getActiveCached()
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, self::CACHE_TTL, function () {
            return self::where('is_active', true)->first();
        });
    }

    /**
     * Clear cache saat data berubah
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget(self::CACHE_KEY_ACTIVE);
            \App\Services\CacheService::clearAcademicCache();
        });

        static::deleted(function () {
            Cache::forget(self::CACHE_KEY_ACTIVE);
            \App\Services\CacheService::clearAcademicCache();
        });
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
        public function cbtExams()
        {
            return $this->hasMany(CbtExam::class, 'semester_id');
        }

        /**
         * Fallback for name attribute.
         */
        public function getNameAttribute()
        {
            return $this->semester_name;
        }
}
