<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantTestScore extends Model
{
    protected $fillable = [
        'applicant_id',
        'admission_test_id',
        'score',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /**
     * Get the applicant that owns the test score
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the admission test
     */
    public function admissionTest(): BelongsTo
    {
        return $this->belongsTo(AdmissionTest::class);
    }

    /**
     * Get weighted score
     */
    public function getWeightedScoreAttribute(): float
    {
        if (!$this->admissionTest) {
            return 0;
        }
        
        return $this->score * $this->admissionTest->weight;
    }
}
