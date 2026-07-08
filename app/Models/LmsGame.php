<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'course_id',
        'module_id',
        'title',
        'description',
        'game_type',
        'game_data',
        'reward_points',
        'time_limit',
        'lives_count',
        'is_published',
        'is_daily_challenge',
    ];

    protected $casts = [
        'game_data' => 'array',
        'is_published' => 'boolean',
        'is_daily_challenge' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'module_id');
    }

    public function attempts()
    {
        return $this->hasMany(LmsGameAttempt::class, 'game_id');
    }
}
