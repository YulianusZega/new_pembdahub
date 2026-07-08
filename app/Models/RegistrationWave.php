<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistrationWave extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'wave_number',
        'start_date',
        'end_date',
        'quota',
        'registered_count',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'quota' => 'integer',
        'registered_count' => 'integer',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class, 'wave_id');
    }

    // Methods
    public function isOpen()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function isFull()
    {
        if ($this->quota === null) {
            return false; // Unlimited
        }

        return $this->registered_count >= $this->quota;
    }

    public function getRemainingQuota()
    {
        if ($this->quota === null) {
            return null; // Unlimited
        }

        return max(0, $this->quota - $this->registered_count);
    }

    public function getStatusLabel()
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }

        if ($this->isFull()) {
            return 'Kuota Penuh';
        }

        if ($this->isOpen()) {
            return 'Dibuka';
        }

        $now = Carbon::now();
        if ($now->lt($this->start_date)) {
            return 'Belum Dibuka';
        }

        return 'Ditutup';
    }

    public function getStatusBadgeColor()
    {
        $status = $this->getStatusLabel();

        return match($status) {
            'Dibuka' => 'bg-green-100 text-green-700',
            'Kuota Penuh' => 'bg-red-100 text-red-700',
            'Ditutup' => 'bg-gray-100 text-gray-700',
            'Belum Dibuka' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
