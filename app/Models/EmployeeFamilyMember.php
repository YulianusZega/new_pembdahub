<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'relation',
        'full_name',
        'birth_date',
        'gender',
        'occupation',
        'education_level',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public const RELATIONS = [
        'suami' => 'Suami',
        'istri' => 'Istri',
        'anak' => 'Anak',
        'ayah' => 'Ayah',
        'ibu' => 'Ibu',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRelationLabelAttribute(): string
    {
        return self::RELATIONS[$this->relation] ?? $this->relation;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
}
