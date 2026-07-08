<?php

namespace App\Http\Controllers\Yayasan;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Employee;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get current academic year (cached)
        $currentAcademicYear = Cache::remember('current_academic_year', 3600, function () {
            return AcademicYear::where('is_active', true)->first();
        });

        // Get all 3 schools (excluding yayasan)
        $schools = School::schoolsOnly()->where('is_active', true)->get();

        // Get yayasan record
        $yayasan = School::yayasanOnly()->first();

        // Aggregate statistics across all schools
        $cacheKey = "yayasan_dashboard_stats_" . now()->format('Y_m_d');
        $stats = Cache::remember($cacheKey, 300, function () use ($schools) {
            $schoolIds = $schools->pluck('id');

            return [
                'total_schools' => $schools->count(),
                'total_students' => Student::whereIn('school_id', $schoolIds)
                    ->where('status', 'aktif')
                    ->count(),
                'total_employees' => Employee::whereIn('school_id', $schoolIds)
                    ->where('is_active', true)
                    ->count(),
            ];
        });

        // Per-school summary
        $calendarService = app(\App\Services\EducationalCalendarService::class);
        $schoolSummaries = $schools->map(function ($school) use ($currentAcademicYear, $calendarService) {
            $activeDays = $currentAcademicYear ? $calendarService->calculateActiveDays($school, $currentAcademicYear) : 0;
            return [
                'id' => $school->id,
                'name' => $school->name,
                'type' => strtoupper($school->type),
                'student_count' => Student::where('school_id', $school->id)
                    ->where('status', 'aktif')
                    ->count(),
                'employee_count' => Employee::where('school_id', $school->id)
                    ->where('is_active', true)
                    ->count(),
                'active_days' => $activeDays,
            ];
        });

        return view('yayasan.dashboard', compact(
            'stats',
            'schools',
            'schoolSummaries',
            'currentAcademicYear',
            'yayasan'
        ));
    }
}
