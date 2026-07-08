<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'school_id',
        'teacher_type',
    ];

    /**
     * Relationship: Response belongs to Survey
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Relationship: Response belongs to User (Respondent)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Response belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: Response has many Answers
     */
    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }
}
