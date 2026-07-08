<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniProfile extends Model
{
    use HasFactory;

    protected $table = 'alumni_profiles';

    protected $fillable = [
        'student_id',
        'school_id',
        'full_name',
        'graduation_year',
        'phone',
        'email',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function tracerStudies()
    {
        return $this->hasMany(TracerStudy::class, 'alumni_profile_id');
    }
}
