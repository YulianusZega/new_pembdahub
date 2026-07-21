<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BlockSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'name',
        'start_date',
        'end_date',
        'swap_interval_weeks',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function studentGroups()
    {
        return $this->hasMany(BlockStudentGroup::class);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getWeekNumber($date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        } elseif (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if ($date->lt($this->start_date)) {
            return 1;
        }

        // Days diff from start date
        $daysDiff = $this->start_date->diffInDays($date);
        
        // Calculate week number (1-based)
        return (int)floor($daysDiff / 7) + 1;
    }

    public function getActiveRotationForDate($date = null)
    {
        $weekNumber = $this->getWeekNumber($date);
        
        // e.g. if swap_interval_weeks is 2
        // weeks 1, 2 = (1-1)/2 = 0 -> even -> normal
        // weeks 3, 4 = (3-1)/2 = 1 -> odd -> swapped
        // weeks 5, 6 = (5-1)/2 = 2 -> even -> normal
        $periodIndex = (int)floor(($weekNumber - 1) / $this->swap_interval_weeks);

        return ($periodIndex % 2 === 0) ? 'normal' : 'swapped';
    }

    public function getSwapPeriods()
    {
        $periods = [];
        $currentDate = $this->start_date->copy();
        $endDate = $this->end_date->copy();
        $weekNumber = 1;

        while ($currentDate->lte($endDate)) {
            $periodEnd = $currentDate->copy()->addWeeks($this->swap_interval_weeks)->subDay();
            if ($periodEnd->gt($endDate)) {
                $periodEnd = $endDate->copy();
            }

            $periodIndex = (int)floor(($weekNumber - 1) / $this->swap_interval_weeks);
            $rotation = ($periodIndex % 2 === 0) ? 'normal' : 'swapped';

            $periods[] = [
                'start' => $currentDate->copy(),
                'end' => $periodEnd->copy(),
                'rotation' => $rotation,
                'week_number' => $weekNumber,
            ];

            $currentDate->addWeeks($this->swap_interval_weeks);
            $weekNumber += $this->swap_interval_weeks;
        }

        return $periods;
    }
}
