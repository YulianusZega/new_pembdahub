<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Reputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'level_name',
        'rank_global',
    ];

    /**
     * Relationship: Reputation belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Get color based on level
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level_name) {
            'Emerald Elite' => 'emerald',
            'Legendary Scholar' => 'amber',
            'Ace Specialist' => 'indigo',
            'Rising Star' => 'blue',
            default => 'slate',
        };
    }

    /**
     * Logic: Update level based on points
     */
    public function updateLevel(): void
    {
        $points = $this->total_points;
        
        if ($points >= 5000) {
            $this->level_name = 'Emerald Elite';
        } elseif ($points >= 2000) {
            $this->level_name = 'Legendary Scholar';
        } elseif ($points >= 1000) {
            $this->level_name = 'Ace Specialist';
        } elseif ($points >= 500) {
            $this->level_name = 'Rising Star';
        } else {
            $this->level_name = 'Newbie';
        }
    }

    /**
     * Helper: Calculate progress percentage to next level
     */
    public function getProgressPercentageAttribute(): int
    {
        $points = $this->total_points;
        
        if ($points >= 5000) {
            return 100;
        }
        
        $currentFloor = 0;
        $nextCeil = 500;
        
        if ($points >= 2000) {
            $currentFloor = 2000;
            $nextCeil = 5000;
        } elseif ($points >= 1000) {
            $currentFloor = 1000;
            $nextCeil = 2000;
        } elseif ($points >= 500) {
            $currentFloor = 500;
            $nextCeil = 1000;
        }
        
        $progress = (($points - $currentFloor) / ($nextCeil - $currentFloor)) * 100;
        return (int) min(100, max(0, $progress));
    }
}
