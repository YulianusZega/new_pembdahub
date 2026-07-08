<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklLog extends Model
{
    use HasFactory;

    protected $table = 'pkl_logs';

    protected $fillable = [
        'pkl_placement_id',
        'log_date',
        'activity',
        'photo',
        'latitude',
        'longitude',
        'status',
        'mentor_notes',
        'approved_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'approved_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function placement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && \Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
}
