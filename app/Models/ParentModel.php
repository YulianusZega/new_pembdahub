<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'student_id',
        'relation_type',
        'full_name',
        'phone',
        'email',
        'occupation',
        'address',
    ];

    /**
     * Relationship: Orang Tua belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Orang Tua belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get relation type label
     */
    public function getRelationTypeLabel(): string
    {
        $labels = [
            'ayah' => 'Ayah',
            'ibu' => 'Ibu',
            'wali' => 'Wali',
        ];
        return $labels[$this->relation_type] ?? $this->relation_type;
    }
}
