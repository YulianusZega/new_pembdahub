<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklMonitoring extends Model
{
    protected $table = 'pkl_monitorings';

    protected $fillable = [
        'teacher_id',
        'dudi_id',
        'shift',
        'monitoring_date',
        'assignment_letter_path',
        'photo_path',
        'notes',
        'status',
    ];

    protected $casts = [
        'monitoring_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function dudi()
    {
        return $this->belongsTo(Dudi::class);
    }

    public function getPeriodeKeAttribute()
    {
        $startDate = \Carbon\Carbon::parse('2026-07-20');
        
        if (!$this->monitoring_date || $this->monitoring_date->lessThan($startDate)) {
            return 1;
        }

        $diffInDays = $startDate->diffInDays($this->monitoring_date);
        
        // 1 periode = 13 hari (contoh: 20 Juli - 1 Agustus)
        // Hari ke-0 s/d ke-12 = periode 1
        // Hari ke-13 s/d ke-25 = periode 2, dst.
        $periode = floor($diffInDays / 13) + 1;
        
        // Batas maksimal periode adalah 8
        return min(8, $periode);
    }
}
