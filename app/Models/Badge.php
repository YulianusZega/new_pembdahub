<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'color',
        'description',
        'requirement_type',
        'requirement_value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requirement_value' => 'integer',
    ];

    /**
     * Relationship: Badge has many users through UserBadge
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at');
    }

    /**
     * Logic: Check if user is eligible for this badge
     */
    public function checkEligibility($user)
    {
        if (!$user) return false;

        switch ($this->requirement_type) {
            case 'points':
                return ($user->reputation->total_points ?? 0) >= $this->requirement_value;
            
            case 'attendance':
                // Custom logic for attendance counts if needed
                return false;
                
            default:
                return false;
        }
    }
}
