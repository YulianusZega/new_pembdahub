<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramKeahlian extends Model
{
    use HasFactory;

    protected $table = 'program_keahlians';
    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function konsentrasiKeahlians()
    {
        return $this->hasMany(KonsentrasiKeahlian::class);
    }
}
