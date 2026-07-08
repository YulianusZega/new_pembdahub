<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantPayment extends Model
{
    protected $fillable = [
        'applicant_id',
        'admission_fee_id',
        'payment_type',
        'amount',
        'payment_method',
        'payment_date',
        'payment_proof',
        'bank_name',
        'account_number',
        'account_name',
        'transaction_id',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the payment
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the admission fee
     */
    public function admissionFee(): BelongsTo
    {
        return $this->belongsTo(AdmissionFee::class);
    }

    /**
     * Get the user who verified the payment
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if payment is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Scope for verified payments
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope for unverified payments
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Payment methods
     */
    public static function paymentMethods(): array
    {
        return [
            'transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
        ];
    }
}
