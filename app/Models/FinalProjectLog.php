<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalProjectLog extends Model
{
    use HasFactory;

    protected $table = 'final_project_logs';

    protected $fillable = [
        'final_project_id',
        'log_date',
        'stage',
        'activity',
        'documentation_file',
        'status',
        'advisor_feedback',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function finalProject()
    {
        return $this->belongsTo(FinalProject::class, 'final_project_id');
    }
}
