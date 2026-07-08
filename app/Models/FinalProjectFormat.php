<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalProjectFormat extends Model
{
    use HasFactory;

    protected $table = 'final_project_formats';

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'file_path',
        'created_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
