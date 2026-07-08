<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtQuestionOption extends Model
{
    use HasFactory;

    protected $table = 'cbt_question_options';
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'option_label',
        'option_text',
        'option_image',
        'is_correct',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Media URL Accessor
    public function getOptionImageUrlAttribute(): ?string
    {
        return $this->option_image ? asset('storage/' . $this->option_image) : null;
    }

    // Relationships
    public function question(): BelongsTo
    {
        return $this->belongsTo(CbtQuestion::class, 'question_id');
    }
}
