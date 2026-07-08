<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'alumni';

    protected $fillable = [
        'student_id',
        'school_id',
        'nisn',
        'nis',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'phone',
        'entry_year',
        'graduation_year',
        'final_class',
        'notes',
        'moved_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'moved_at' => 'datetime',
        'entry_year' => 'integer',
        'graduation_year' => 'integer',
    ];

    /**
     * Relationship: Alumni came from Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Alumni belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
