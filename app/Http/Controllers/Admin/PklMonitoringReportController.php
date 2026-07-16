<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PklMonitoring;
use App\Models\PklPlacement;
use App\Models\Teacher;
use App\Models\Dudi;
use Illuminate\Support\Facades\DB;

class PklMonitoringReportController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        
        // View summary of all teachers who have PKL placements in active year
        $teachersQuery = Teacher::whereHas('pklPlacements', function($q) use ($activeYear) {
            if ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            }
        });
        
        $teachers = $teachersQuery->with(['pklPlacements' => function($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                }
                $q->select('teacher_id', 'dudi_id', 'shift')->distinct();
            }, 'user'])
            ->withCount('pklMonitorings')
            ->paginate(15);
            
        return view('admin.pkl_monitorings.index', compact('teachers', 'activeYear'));
    }

    public function show(Teacher $teacher, Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        
        // View detailed monitoring reports submitted by a specific teacher
        $placementsQuery = PklPlacement::with('dudi')
            ->where('teacher_id', $teacher->id);
            
        if ($activeYear) {
            $placementsQuery->where('academic_year_id', $activeYear->id);
        }
            
        $placements = $placementsQuery->select('dudi_id', 'shift', DB::raw('count(*) as total_students'), DB::raw('MAX(is_perangkat_ready) as is_perangkat_ready'), DB::raw('MAX(perangkat_file_path) as perangkat_file_path'))
            ->groupBy('dudi_id', 'shift')
            ->get();

        $monitorings = PklMonitoring::with('dudi')
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('monitoring_date')
            ->paginate(20);

        return view('admin.pkl_monitorings.show', compact('teacher', 'placements', 'monitorings', 'activeYear'));
    }
}
