<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefaEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_name',
        'name',
        'rfid_uid',
        'position',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(TefaAttendance::class, 'tefa_employee_id');
    }

    public function todayAttendance()
    {
        return $this->hasOne(TefaAttendance::class, 'tefa_employee_id')->where('date', now()->toDateString());
    }
}
