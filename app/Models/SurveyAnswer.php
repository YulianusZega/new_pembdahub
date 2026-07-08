<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'question_id',
        'rating',
        'answer_text',
        'essay_score',
    ];

    /**
     * Relationship: Answer belongs to Response
     */
    public function response()
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    /**
     * Relationship: Answer belongs to Question
     */
    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
