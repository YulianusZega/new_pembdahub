<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Centralized cache management.
 * 
 * Call these methods when data changes to ensure dashboard caches stay fresh.
 */
class CacheService
{
    /**
     * Clear all treasurer-related caches for a school.
     */
    public static function clearTreasurerCache(int $schoolId): void
    {
        $month = now()->month;
        $year = now()->year;
        Cache::forget("treasurer_stats_{$schoolId}_{$year}_{$month}");
    }

    /**
     * Clear PSB applicant count caches.
     */
    public static function clearPSBCache(): void
    {
        Cache::forget('psb_total_count');
        Cache::forget('psb_smp_count');
        Cache::forget('psb_sma_count');
        Cache::forget('psb_smk_count');
    }

    /**
     * Clear academic year / semester caches.
     */
    public static function clearAcademicCache(): void
    {
        Cache::forget('active_academic_year');
        Cache::forget('active_semester');
        Cache::forget('current_academic_year');
        Cache::forget('report_card_semesters');
    }

    /**
     * Clear all application caches.
     */
    public static function clearAll(): void
    {
        Cache::flush();
    }
}
