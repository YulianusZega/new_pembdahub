<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_material_id',
        'user_id',
        'ip_address',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMaterial::class, 'knowledge_material_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
