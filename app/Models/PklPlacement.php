<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklPlacement extends Model
{
    use HasFactory;

    protected $table = 'pkl_placements';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'dudi_id',
        'company_name',
        'shift',
        'is_perangkat_ready',
        'company_address',
        'mentor_name',
        'mentor_phone',
        'start_date',
        'end_date',
        'teacher_id',
        'status',
        'signed_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_perangkat_ready' => 'boolean',
    ];

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function logs()
    {
        return $this->hasMany(PklLog::class, 'pkl_placement_id');
    }

    public function grade()
    {
        return $this->hasOne(PklGrade::class, 'pkl_placement_id');
    }
}
