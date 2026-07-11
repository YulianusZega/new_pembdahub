<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'grid_x',
        'grid_y',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pieces()
    {
        return $this->hasMany(PuzzlePiece::class);
    }

    public function getProgressAttribute()
    {
        $total = $this->grid_x * $this->grid_y;
        $placed = $this->pieces()->where('is_placed', true)->count();
        return [
            'total' => $total,
            'placed' => $placed,
            'percentage' => $total > 0 ? round(($placed / $total) * 100) : 0,
        ];
    }
}
