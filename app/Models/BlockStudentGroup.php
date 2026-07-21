<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockStudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_schedule_id',
        'classroom_id',
        'student_id',
        'group',
    ];

    public function blockSchedule()
    {
        return $this->belongsTo(BlockSchedule::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeForClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    public function scopeGroupA($query)
    {
        return $query->where('group', 'A');
    }

    public function scopeGroupB($query)
    {
        return $query->where('group', 'B');
    }
}
