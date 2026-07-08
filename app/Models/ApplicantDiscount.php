<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDiscount extends Model
{
    protected $fillable = [
        'applicant_id',
        'admission_discount_id',
        'discount_type',
        'discount_value',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the discount
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the admission discount
     */
    public function admissionDiscount(): BelongsTo
    {
        return $this->belongsTo(AdmissionDiscount::class);
    }

    /**
     * Get the user who approved the discount
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if discount is approved
     */
    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    /**
     * Scope for approved discounts
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope for pending discounts
     */
    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }
}
