<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'school_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    protected const ACTIONS = [
        'create' => 'Membuat',
        'update' => 'Mengubah',
        'delete' => 'Menghapus',
        'view' => 'Melihat',
        'download' => 'Mengunduh',
        'upload' => 'Mengunggah',
        'login' => 'Masuk',
        'logout' => 'Keluar',
    ];

    /**
     * Relationship: ActivityLog belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: ActivityLog belongs to School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get action label
     */
    public function getActionLabel()
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    /**
     * Scope: Get logs by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get logs by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('logged_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Get recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('logged_at', '>=', now()->subDays($days));
    }
}
