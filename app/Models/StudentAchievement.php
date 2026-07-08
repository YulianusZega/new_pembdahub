<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    use HasFactory;

    protected $table = 'student_achievements';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'title',
        'type',
        'level',
        'rank',
        'achievement_date',
        'description',
        'certificate_file',
        'created_by',
    ];

    protected $casts = [
        'achievement_date' => 'date',
    ];

    /**
     * Relationship: Achievement belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Achievement belongs to AcademicYear
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relationship: Achievement created by User
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'academic' => 'Akademik',
            'sport' => 'Olahraga',
            'art' => 'Seni',
            'competition' => 'Kompetisi',
            'other' => 'Lainnya',
            default => '-',
        };
    }

    /**
     * Get level label
     */
    public function getLevelLabelAttribute()
    {
        return match($this->level) {
            'school' => 'Sekolah',
            'district' => 'Kecamatan',
            'city' => 'Kota/Kabupaten',
            'province' => 'Provinsi',
            'national' => 'Nasional',
            'international' => 'Internasional',
            default => '-',
        };
    }

    /**
     * Get rank label
     */
    public function getRankLabelAttribute()
    {
        return match($this->rank) {
            'winner' => 'Juara 1',
            'runner_up' => 'Juara 2',
            'third_place' => 'Juara 3',
            'participant' => 'Peserta',
            default => '-',
        };
    }

    /**
     * Get level badge class
     */
    public function getLevelBadgeAttribute()
    {
        return match($this->level) {
            'international' => 'badge-danger',
            'national' => 'badge-warning',
            'province' => 'badge-info',
            'city' => 'badge-primary',
            'district', 'school' => 'badge-secondary',
            default => 'badge-light',
        };
    }

    /**
     * Scope: Filter by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}
