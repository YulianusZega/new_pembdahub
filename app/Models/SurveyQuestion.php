<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_text',
        'type', // 'scale', 'text'
        'scale_type',
        'target_guru',
        'order',
    ];

    /**
     * Relationship: Question belongs to Survey
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Relationship: Question has many Answers
     */
    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }
}
