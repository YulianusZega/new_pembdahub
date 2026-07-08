<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'education_level',
        'institution_name',
        'major',
        'graduation_year',
        'gpa',
        'certificate_number',
        'certificate_file',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
        'gpa' => 'decimal:2',
    ];

    public const LEVELS = [
        'SD' => 'SD',
        'SMP' => 'SMP',
        'SMA' => 'SMA/SMK',
        'D1' => 'D1',
        'D2' => 'D2',
        'D3' => 'D3',
        'D4' => 'D4',
        'S1' => 'S1',
        'S2' => 'S2',
        'S3' => 'S3',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->education_level] ?? $this->education_level;
    }
}
