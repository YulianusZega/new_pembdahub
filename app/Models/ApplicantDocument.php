<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDocument extends Model
{
    protected $fillable = [
        'applicant_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'verified',
        'verified_at',
        'verified_by',
        'notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'file_size' => 'integer',
        'verified' => 'boolean',
    ];

    /**
     * Get the applicant that owns the document
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the user who verified the document
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if document is verified
     */
    public function isVerified(): bool
    {
        return $this->verified === true;
    }

    /**
     * Scope for verified documents
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope for unverified documents
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Get the human-readable label for the document type.
     */
    public function getLabelAttribute(): string
    {
        $school = $this->applicant?->school;
        if ($school) {
            $allSchoolDocs = $school->getAllDocumentTypes();
            if (isset($allSchoolDocs[$this->document_type])) {
                return $allSchoolDocs[$this->document_type];
            }
        }

        return match ($this->document_type) {
            'kk' => 'Fotocopy Kartu Keluarga (KK)',
            'akta' => 'Fotocopy Akta Kelahiran',
            'ijazah' => 'Ijazah / SKHU / SKL',
            'raport' => 'Raport Semester Terakhir',
            'photo' => 'Pas Foto 3x4 (Warna)',
            'certificate' => 'Sertifikat Prestasi (Opsional)',
            default => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }
}

