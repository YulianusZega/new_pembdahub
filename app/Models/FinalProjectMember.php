<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalProjectMember extends Model
{
    use HasFactory;

    protected $table = 'final_project_members';

    protected $fillable = [
        'final_project_id',
        'student_id',
        'role',
    ];

    public function finalProject()
    {
        return $this->belongsTo(FinalProject::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
