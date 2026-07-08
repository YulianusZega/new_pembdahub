<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'attendances';

    protected $fillable = [
        'student_id',
        'classroom_id',
        'schedule_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'recorded_via',
        'device_id',
        'latitude',
        'longitude',
        'notes',
        'attachment',
        'attachment_name',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected const STATUSES = [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'alpha' => 'Absen',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'libur' => 'Libur',
        'pulang' => 'Dipulangkan',
    ];

    /**
     * Accessor for backward compatibility: attendance_date -> date
     */
    public function getAttendanceDateAttribute()
    {
        return $this->date;
    }

    /**
     * Relationship: Attendance belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Attendance belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship: Attendance belongs to User (recorded by)
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if student is present
     */
    public function isPresent()
    {
        return in_array($this->status, ['hadir', 'terlambat']);
    }

    /**
     * Check if student is absent
     */
    public function isAbsent()
    {
        return $this->status === 'alpha';
    }

    /**
     * Scope: Get attendance by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope: Get attendance by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get attendance for date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
