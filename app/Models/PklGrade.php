<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklGrade extends Model
{
    use HasFactory;

    protected $table = 'pkl_grades';

    protected $fillable = [
        'pkl_placement_id',
        'score_discipline',
        'score_teamwork',
        'score_technical',
        'score_safety',
        'score_average',
        'notes',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score_average' => 'float',
    ];

    public function placement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }
}
