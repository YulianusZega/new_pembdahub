<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'subjects';

    protected $fillable = [
        'school_id',
        'major_id',
        'program_keahlian_id',
        'code',
        'name',
        'category',
        'hours_per_week',
        'subject_code',
        'subject_name',
        'description',
        'kkm',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kkm' => 'integer',
    ];

    /**
     * Accessor: ensure $subject->name always returns a display value.
     * Falls back to subject_name when name column is null.
     */
    public function getNameAttribute($value): string
    {
        return $value ?: $this->subject_name ?: '-';
    }

    /**
     * Accessor: fall back to name when subject_name is empty
     */
    public function getSubjectNameAttribute($value): string
    {
        return $value ?: ($this->attributes['name'] ?? '-');
    }

    /**
     * Accessor: fall back to code when subject_code is empty
     */
    public function getSubjectCodeAttribute($value): string
    {
        return $value ?: ($this->attributes['code'] ?? '-');
    }

    /**
     * Subject belongs to a School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Subject belongs to a Major
     */
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Subject belongs to a Program Keahlian (SMK)
     */
    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    /**
     * Optional relationship: teachers teaching this subject (via schedules) - DEPRECATED
     * Use competentTeachers() for teachers with competency in this subject
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'schedules', 'subject_id', 'teacher_id')
            ->withPivot('classroom_id', 'day_of_week', 'start_time', 'end_time')
            ->withTimestamps();
    }

    /**
     * Relationship: Guru yang berkompeten mengajar mata pelajaran ini (NEW)
     * Many-to-Many through subject_teacher pivot table
     */
    public function competentTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_id', 'teacher_id')
            ->withTimestamps()
            ->where('teachers.is_active', 1)
            ->orderBy('teachers.full_name');
    }

    /**
     * Helper: Get teachers with this as primary subject
     */
    public function getPrimaryTeachers()
    {
        return $this->competentTeachers()->get();
    }

    /**
     * Optional relationship: grades for this subject
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    
        public function cbtExams()
        {
            return $this->hasMany(\App\Models\CbtExam::class, 'subject_id');
        }
    
        public function cbtQuestionBanks()
        {
            return $this->hasMany(\App\Models\CbtQuestionBank::class, 'subject_id');
        }
}
