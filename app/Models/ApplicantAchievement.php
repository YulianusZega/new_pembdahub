<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantAchievement extends Model
{
    protected $fillable = [
        'applicant_id',
        'achievement_type',
        'achievement_name',
        'achievement_level',
        'rank',
        'organizer',
        'year',
        'certificate_path',
        'points',
    ];

    protected $casts = [
        'year' => 'integer',
        'points' => 'decimal:2',
    ];

    /**
     * Get the applicant that owns the achievement
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Achievement types
     */
    public static function achievementTypes(): array
    {
        return [
            'academic' => 'Akademik',
            'sports' => 'Olahraga',
            'arts' => 'Seni & Budaya',
            'science' => 'Sains & Teknologi',
            'religion' => 'Keagamaan',
            'other' => 'Lainnya',
        ];
    }

    /**
     * Achievement levels
     */
    public static function achievementLevels(): array
    {
        return [
            'international' => 'Internasional',
            'national' => 'Nasional',
            'provincial' => 'Provinsi',
            'district' => 'Kabupaten/Kota',
            'school' => 'Sekolah',
        ];
    }

    /**
     * Achievement ranks (Juara)
     */
    public static function achievementRanks(): array
    {
        return [
            '1' => 'Juara 1',
            '2' => 'Juara 2',
            '3' => 'Juara 3',
            'harapan_1' => 'Harapan 1',
            'harapan_2' => 'Harapan 2',
            'harapan_3' => 'Harapan 3',
            'partisipan' => 'Partisipan',
        ];
    }

    /**
     * Check if achievement is eligible for fee exemption
     */
    public function isEligibleForFeeExemption(): bool
    {
        // Only rank 1, 2, 3 are eligible
        return in_array($this->rank, ['1', '2', '3']);
    }

    /**
     * Get formatted rank name
     */
    public function getFormattedRankAttribute(): string
    {
        $ranks = self::achievementRanks();
        return $ranks[$this->rank] ?? $this->rank;
    }
}
