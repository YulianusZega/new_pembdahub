<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalCalendar extends Model
{
    use HasFactory;

    protected $table = 'educational_calendars';

    protected $fillable = [
        'academic_year_id',
        'school_id',
        'title',
        'start_date',
        'end_date',
        'type',
        'is_holiday',
        'level',
        'created_by',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_holiday' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
