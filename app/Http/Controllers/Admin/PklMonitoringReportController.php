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
            }, 'pklPlacements.dudi', 'user'])
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
            
        $placements = PklPlacement::with(['dudi', 'student.classroom'])
            ->where('teacher_id', $teacher->id)
            ->when($activeYear, function($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })
            ->get()
            ->groupBy(function($item) {
                return $item->dudi_id . '|' . $item->shift;
            });

        $monitorings = PklMonitoring::with('dudi')
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('monitoring_date')
            ->paginate(20);

        return view('admin.pkl_monitorings.show', compact('teacher', 'placements', 'monitorings', 'activeYear'));
    }
}
