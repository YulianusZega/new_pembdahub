<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantFeeExemption extends Model
{
    protected $fillable = [
        'applicant_id',
        'exemption_rule_id',
        'achievement_id',
        'rank_achieved',
        'proof_document_type',
        'proof_document_path',
        'original_fee_amount',
        'exemption_amount',
        'final_fee_amount',
        'verified',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'original_fee_amount' => 'decimal:2',
        'exemption_amount' => 'decimal:2',
        'final_fee_amount' => 'decimal:2',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the exemption rule
     */
    public function exemptionRule(): BelongsTo
    {
        return $this->belongsTo(AchievementFeeExemptionRule::class, 'exemption_rule_id');
    }

    /**
     * Get the achievement that qualified for exemption
     */
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(ApplicantAchievement::class, 'achievement_id');
    }

    /**
     * Get the user who verified
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if exemption is verified
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Get savings amount
     */
    public function getSavingsAttribute(): float
    {
        return $this->original_fee_amount - $this->final_fee_amount;
    }
}
