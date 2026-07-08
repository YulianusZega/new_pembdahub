<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerStudy extends Model
{
    use HasFactory;

    protected $table = 'tracer_studies';

    protected $fillable = [
        'alumni_profile_id',
        'employment_status',
        'company_name',
        'job_title',
        'salary_range',
        'university_name',
        'major',
        'wirausaha_field',
        'feedback_for_school',
        'survey_date',
    ];

    protected $casts = [
        'survey_date' => 'date',
    ];

    public function alumni()
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_profile_id');
    }
}
