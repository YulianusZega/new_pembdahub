<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'student_classes';

    protected $fillable = [
        'student_id',
        'classroom_id',
        'academic_year_id',
        'status',
        'entry_date',
        'promoted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'promoted_at' => 'datetime',
    ];

    protected const STATUSES = [
        'aktif'   => 'Aktif',
        'naik'    => 'Naik Kelas',
        'tinggal' => 'Tinggal Kelas',
        'pindah'  => 'Pindah',
        'keluar'  => 'Keluar',
        'lulus'   => 'Lulus',
    ];

    /**
     * Relationship: StudentClass belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: StudentClass belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship: StudentClass belongs to AcademicYear
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if student is active in this class
     */
    public function isActive()
    {
        return $this->status === 'aktif';
    }

    /**
     * Scope: Get active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope: Get promoted students
     */
    public function scopePromoted($query)
    {
        return $query->where('status', 'naik')->whereNotNull('promoted_at');
    }
}
