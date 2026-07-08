<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\AchievementFeeExemptionRule;
use App\Models\ApplicantFeeExemption;
use App\Models\ApplicantAchievement;
use App\Models\AdmissionFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AchievementFeeExemptionService
{
    /**
     * Check if applicant is eligible for fee exemption based on achievements
     */
    public function checkEligibility(Applicant $applicant): ?AchievementFeeExemptionRule
    {
        // Only for 'prestasi' admission path
        if ($applicant->admission_path !== 'prestasi') {
            return null;
        }

        // Get all active exemption rules for target school and academic year
        $rules = AchievementFeeExemptionRule::where('target_school_id', $applicant->school_id)
            ->where('academic_year_id', $applicant->academic_year_id)
            ->where('is_active', true)
            ->get();

        if ($rules->isEmpty()) {
            return null;
        }

        // Get applicant's previous school info
        $previousSchool = $applicant->previous_school;
        $previousSchoolLevel = $this->determinePreviousSchoolLevel($applicant->school->type);

        // Check each rule
        foreach ($rules as $rule) {
            if ($rule->matchesPreviousSchool($previousSchool, $previousSchoolLevel)) {
                // Check if applicant has eligible achievements
                $eligibleAchievement = $this->findEligibleAchievement($applicant, $rule);
                
                if ($eligibleAchievement) {
                    return $rule;
                }
            }
        }

        return null;
    }

    /**
     * Find eligible achievement for exemption rule
     */
    private function findEligibleAchievement(Applicant $applicant, AchievementFeeExemptionRule $rule): ?ApplicantAchievement
    {
        return ApplicantAchievement::where('applicant_id', $applicant->id)
            ->whereIn('rank', $rule->eligible_ranks)
            ->whereNotNull('certificate_path')
            ->first();
    }

    /**
     * Determine previous school level based on target school type
     */
    private function determinePreviousSchoolLevel(string $targetSchoolType): string
    {
        if (stripos($targetSchoolType, 'SMP') !== false) {
            return 'SD';
        }
        
        if (stripos($targetSchoolType, 'SMA') !== false || stripos($targetSchoolType, 'SMK') !== false) {
            return 'SMP';
        }

        return 'unknown';
    }

    /**
     * Apply fee exemption to applicant
     */
    public function applyExemption(
        Applicant $applicant, 
        AchievementFeeExemptionRule $rule,
        ApplicantAchievement $achievement,
        ?string $proofDocumentPath = null
    ): ApplicantFeeExemption {
        DB::beginTransaction();
        
        try {
            // Get registration fee
            $registrationFee = AdmissionFee::where('school_id', $applicant->school_id)
                ->where('academic_year_id', $applicant->academic_year_id)
                ->where('fee_type', 'registration')
                ->first();

            $originalAmount = $registrationFee ? $registrationFee->amount : 50000;
            
            // Calculate exemption
            $exemptionAmount = $rule->exemption_type === 'full' 
                ? $rule->exemption_amount 
                : ($originalAmount * $rule->exemption_amount / 100);

            $finalAmount = max(0, $originalAmount - $exemptionAmount);

            // Create or update exemption record
            $exemption = ApplicantFeeExemption::updateOrCreate(
                [
                    'applicant_id' => $applicant->id,
                    'exemption_rule_id' => $rule->id,
                ],
                [
                    'achievement_id' => $achievement->id,
                    'rank_achieved' => $achievement->rank,
                    'proof_document_type' => $achievement->certificate_path ? 'certificate' : 'raport',
                    'proof_document_path' => $proofDocumentPath ?? $achievement->certificate_path,
                    'original_fee_amount' => $originalAmount,
                    'exemption_amount' => $exemptionAmount,
                    'final_fee_amount' => $finalAmount,
                    'verified' => false,
                ]
            );

            DB::commit();

            Log::info("Fee exemption applied", [
                'applicant_id' => $applicant->id,
                'rule_id' => $rule->id,
                'exemption_id' => $exemption->id,
                'original_amount' => $originalAmount,
                'exemption_amount' => $exemptionAmount,
                'final_amount' => $finalAmount,
            ]);

            return $exemption;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to apply fee exemption", [
                'applicant_id' => $applicant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify fee exemption (admin action)
     */
    public function verifyExemption(ApplicantFeeExemption $exemption, int $verifiedBy, bool $approved, ?string $rejectionReason = null): bool
    {
        DB::beginTransaction();
        
        try {
            $exemption->verified = $approved;
            $exemption->verified_by = $verifiedBy;
            $exemption->verified_at = now();
            
            if (!$approved && $rejectionReason) {
                $exemption->rejection_reason = $rejectionReason;
                // Reset final amount to original if rejected
                $exemption->final_fee_amount = $exemption->original_fee_amount;
            }
            
            $exemption->save();

            DB::commit();

            Log::info("Fee exemption " . ($approved ? 'approved' : 'rejected'), [
                'exemption_id' => $exemption->id,
                'applicant_id' => $exemption->applicant_id,
                'verified_by' => $verifiedBy,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to verify fee exemption", [
                'exemption_id' => $exemption->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get applicable fee amount for applicant (considering exemptions)
     */
    public function getApplicableFeeAmount(Applicant $applicant, string $feeType = 'registration'): float
    {
        // Check if there's an approved exemption
        $exemption = ApplicantFeeExemption::where('applicant_id', $applicant->id)
            ->whereHas('exemptionRule', function($query) use ($feeType) {
                $query->where('exemption_fee_type', $feeType);
            })
            ->where('verified', true)
            ->first();

        if ($exemption) {
            return $exemption->final_fee_amount;
        }

        // Return normal fee
        $fee = AdmissionFee::where('school_id', $applicant->school_id)
            ->where('academic_year_id', $applicant->academic_year_id)
            ->where('fee_type', $feeType)
            ->first();

        return $fee ? $fee->amount : 0;
    }

    /**
     * Auto-check and apply exemption when applicant submits with prestasi path
     */
    public function autoCheckAndApply(Applicant $applicant): ?ApplicantFeeExemption
    {
        $rule = $this->checkEligibility($applicant);
        
        if (!$rule) {
            return null;
        }

        $achievement = $this->findEligibleAchievement($applicant, $rule);
        
        if (!$achievement) {
            return null;
        }

        return $this->applyExemption($applicant, $rule, $achievement);
    }
}
