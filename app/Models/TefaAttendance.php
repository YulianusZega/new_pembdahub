<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefaAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tefa_employee_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'recorded_via',
        'device_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(TefaEmployee::class, 'tefa_employee_id');
    }
}
