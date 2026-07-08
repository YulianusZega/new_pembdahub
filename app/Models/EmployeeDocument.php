<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public const TYPES = [
        'ktp' => 'KTP',
        'npwp' => 'NPWP',
        'kk' => 'Kartu Keluarga',
        'sk_pengangkatan' => 'SK Pengangkatan',
        'sk_jabatan' => 'SK Jabatan',
        'ijazah' => 'Ijazah',
        'sertifikat' => 'Sertifikat',
        'nuptk' => 'NUPTK',
        'kontrak' => 'Kontrak Kerja',
        'lainnya' => 'Lainnya',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->document_type] ?? $this->document_type;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }
}
