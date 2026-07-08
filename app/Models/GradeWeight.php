<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GradeWeight extends Model
{
    use HasFactory;

    protected $table = 'grade_weights';

    protected $fillable = [
        'school_id',
        'tugas_weight',
        'pts_weight',
        'pas_weight',
        'sikap_weight',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'tugas_weight' => 'float',
        'pts_weight' => 'float',
        'pas_weight' => 'float',
        'sikap_weight' => 'float',
    ];

    /**
     * Default weights
     */
    const DEFAULT_TUGAS = 20.0;
    const DEFAULT_PTS = 30.0;
    const DEFAULT_PAS = 40.0;
    const DEFAULT_SIKAP = 10.0;

    /**
     * Cache key prefix and TTL
     */
    const CACHE_PREFIX = 'grade_weights.school.';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Relationship: GradeWeight belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: Updated by User
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get weights for a school (cached), create default if not exists
     */
    public static function getForSchool(int $schoolId): self
    {
        return Cache::remember(self::CACHE_PREFIX . $schoolId, self::CACHE_TTL, function () use ($schoolId) {
            return self::firstOrCreate(
                ['school_id' => $schoolId],
                [
                    'tugas_weight' => self::DEFAULT_TUGAS,
                    'pts_weight' => self::DEFAULT_PTS,
                    'pas_weight' => self::DEFAULT_PAS,
                    'sikap_weight' => self::DEFAULT_SIKAP,
                    'description' => 'Default: Tugas 20% + PTS 30% + PAS 40% + Sikap 10%',
                ]
            );
        });
    }

    /**
     * Get weights as decimal fractions (for calculation)
     */
    public function getWeightsAsDecimal(): array
    {
        return [
            'tugas' => $this->tugas_weight / 100,
            'pts' => $this->pts_weight / 100,
            'pas' => $this->pas_weight / 100,
            'sikap' => $this->sikap_weight / 100,
        ];
    }

    /**
     * Get weights as percentages (for display)
     */
    public function getWeightsAsPercentage(): array
    {
        return [
            'tugas' => $this->tugas_weight,
            'pts' => $this->pts_weight,
            'pas' => $this->pas_weight,
            'sikap' => $this->sikap_weight,
        ];
    }

    /**
     * Validate that weights sum to 100%
     */
    public function isValid(): bool
    {
        $total = $this->tugas_weight + $this->pts_weight + $this->pas_weight + $this->sikap_weight;
        return abs($total - 100.0) < 0.01;
    }

    /**
     * Calculate weighted score using these weights
     */
    public function calculateFinalScore(float $tugas, float $pts, float $pas, float $sikap): float
    {
        $w = $this->getWeightsAsDecimal();
        return round(
            ($tugas * $w['tugas']) + ($pts * $w['pts']) + ($pas * $w['pas']) + ($sikap * $w['sikap']),
            2
        );
    }

    /**
     * Clear cache when updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $model) {
            Cache::forget(self::CACHE_PREFIX . $model->school_id);
        });
    }
}
