<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_time',
        'logout_time',
        'session_id',
        'status',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    protected const STATUSES = [
        'active' => 'Aktif',
        'logout' => 'Logout',
        'timeout' => 'Timeout',
        'force_logout' => 'Dipaksa Logout',
    ];

    /**
     * Relationship: LoginHistory belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get session duration in minutes
     */
    public function getSessionDuration()
    {
        if ($this->logout_time) {
            return $this->logout_time->diffInMinutes($this->login_time);
        }
        return null;
    }

    /**
     * Scope: Get active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('logout_time');
    }

    /**
     * Scope: Get sessions by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get recent sessions
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('login_time', '>=', now()->subDays($days));
    }

    /**
     * Scope: Get sessions from specific IP
     */
    public function scopeFromIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
}
