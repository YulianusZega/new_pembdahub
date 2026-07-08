<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsSubmission;
use App\Models\LmsMaterialProgress;
use App\Models\LmsDiscussion;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LmsMonitoringController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        $query = LmsCourse::query();
        if (!$isSuperAdmin) {
            $query->where('school_id', $schoolId);
        }

        $totalCourses = (clone $query)->count();
        $totalEnrollments = LmsEnrollment::whereHas('lmsClass.course', function($q) use ($isSuperAdmin, $schoolId) {
            if (!$isSuperAdmin) $q->where('school_id', $schoolId);
        })->count();

        $totalSubmissions = LmsSubmission::whereHas('assignment.course', function($q) use ($isSuperAdmin, $schoolId) {
            if (!$isSuperAdmin) $q->where('school_id', $schoolId);
        })->count();

        $totalDiscussions = LmsDiscussion::whereHas('course', function($q) use ($isSuperAdmin, $schoolId) {
            if (!$isSuperAdmin) $q->where('school_id', $schoolId);
        })->count();

        // Top active courses by submissions
        $activeCourses = LmsCourse::with(['teacher.user'])
            ->withCount(['submissions', 'enrollments'])
            ->when(!$isSuperAdmin, function($q) use ($schoolId) {
                return $q->where('school_id', $schoolId);
            })
            ->orderBy('submissions_count', 'desc')
            ->take(5)
            ->get();

        // Latest activities
        $latestSubmissions = LmsSubmission::with(['student.user', 'assignment.course'])
            ->whereHas('student.user')
            ->whereHas('assignment.course', function($q) use ($isSuperAdmin, $schoolId) {
                if (!$isSuperAdmin) $q->where('school_id', $schoolId);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.lms.monitoring', compact(
            'totalCourses',
            'totalEnrollments',
            'totalSubmissions',
            'totalDiscussions',
            'activeCourses',
            'latestSubmissions'
        ));
    }
}
