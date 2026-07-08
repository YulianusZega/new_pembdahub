<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtQuestion extends Model
{
    use HasFactory;

    protected $table = 'cbt_questions';

    protected $fillable = [
        'question_bank_id',
        'question_type',
        'question_text',
        'question_image',
        'question_audio',
        'question_video',
        'explanation',
        'points',
        'difficulty',
        'topic',
        'competency',
        'answer_key',
        'max_words',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    // ==========================================
    // Media URL Accessors
    // ==========================================

    public function getQuestionImageUrlAttribute(): ?string
    {
        return $this->question_image ? asset('storage/' . $this->question_image) : null;
    }

    public function getQuestionAudioUrlAttribute(): ?string
    {
        return $this->question_audio ? asset('storage/' . $this->question_audio) : null;
    }

    public function getQuestionVideoUrlAttribute(): ?string
    {
        return $this->question_video ? asset('storage/' . $this->question_video) : null;
    }

    // ==========================================
    // Relationships
    // ==========================================

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(CbtQuestionBank::class, 'question_bank_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(CbtQuestionOption::class, 'question_id');
    }
}
