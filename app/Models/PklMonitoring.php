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
}
