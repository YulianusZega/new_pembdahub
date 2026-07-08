<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'target_respondent',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Relationship: Survey belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: Survey has many Questions
     */
    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    /**
     * Relationship: Survey has many Responses
     */
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
