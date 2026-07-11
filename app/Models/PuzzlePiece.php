<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuzzlePiece extends Model
{
    use HasFactory;

    protected $fillable = [
        'puzzle_id',
        'piece_index',
        'is_placed',
        'placed_by_user_id',
        'placed_at',
    ];

    protected $casts = [
        'is_placed' => 'boolean',
        'placed_at' => 'datetime',
    ];

    public function puzzle()
    {
        return $this->belongsTo(Puzzle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'placed_by_user_id');
    }
}
