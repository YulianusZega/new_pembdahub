<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonsentrasiKeahlian extends Model
{
    use HasFactory;

    protected $table = 'konsentrasi_keahlians';
    public $timestamps = false;

    protected $fillable = [
        'program_keahlian_id',
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function programKeahlian()
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }
}
