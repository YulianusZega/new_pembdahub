<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementFeeExemptionRule extends Model
{
    protected $fillable = [
        'target_school_id',
        'academic_year_id',
        'previous_school_type',
        'previous_school_name',
        'previous_school_level',
        'eligible_ranks',
        'proof_type',
        'exemption_fee_type',
        'exemption_amount',
        'exemption_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'eligible_ranks' => 'array',
        'exemption_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the target school for this exemption rule
     */
    public function targetSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'target_school_id');
    }

    /**
     * Get the academic year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all exemptions applied using this rule
     */
    public function exemptions(): HasMany
    {
        return $this->hasMany(ApplicantFeeExemption::class, 'exemption_rule_id');
    }

    /**
     * Check if a rank is eligible for this exemption
     */
    public function isRankEligible(string $rank): bool
    {
        return in_array($rank, $this->eligible_ranks);
    }

    /**
     * Check if applicant's previous school matches criteria
     */
    public function matchesPreviousSchool(string $schoolName, string $schoolLevel): bool
    {
        // Check school level
        if ($this->previous_school_level !== $schoolLevel) {
            return false;
        }

        // For Pembda schools, check specific name
        if ($this->previous_school_type === 'pembda') {
            if ($this->previous_school_name) {
                return stripos($schoolName, $this->previous_school_name) !== false;
            }
        }

        // For external schools, any school from that level is eligible
        return true;
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $ranks = implode(', ', array_map(function($rank) {
            return "Juara $rank";
        }, $this->eligible_ranks));

        $school = $this->previous_school_type === 'pembda' 
            ? $this->previous_school_name 
            : "SMP/SD luar";

        return "$ranks dari $school → {$this->targetSchool->name}";
    }
}
