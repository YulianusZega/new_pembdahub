<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'field_of_work',
        'mentor_name',
        'mentor_phone',
        'school_id',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
