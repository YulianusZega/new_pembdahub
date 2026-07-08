<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsMaterialReaction extends Model
{
    use HasFactory;

    protected $table = 'lms_material_reactions';

    protected $fillable = [
        'material_id',
        'student_id',
        'reaction_type',
    ];

    public function material()
    {
        return $this->belongsTo(LmsMaterial::class, 'material_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
