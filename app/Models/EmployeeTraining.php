<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'training_name',
        'organizer',
        'training_type',
        'start_date',
        'end_date',
        'hours',
        'certificate_number',
        'certificate_file',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hours' => 'integer',
    ];

    public const TYPES = [
        'diklat' => 'Diklat',
        'workshop' => 'Workshop',
        'seminar' => 'Seminar',
        'sertifikasi' => 'Sertifikasi',
        'bimtek' => 'Bimbingan Teknis',
        'lainnya' => 'Lainnya',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->training_type] ?? $this->training_type;
    }
}
