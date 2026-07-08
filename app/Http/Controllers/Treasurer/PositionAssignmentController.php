<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class PositionAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get academic years for filter
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', 1)->first();
        
        // Default to current academic year
        $selectedYearId = $request->filled('academic_year_id') 
            ? $request->academic_year_id 
            : ($currentYear ? $currentYear->id : null);
        
        $semester = 'full_year';
        
        // Base query - only teachers (employee_type = 'guru') in treasurer's school
        $query = Employee::with(['school', 'employeePositions' => function ($q) use ($selectedYearId) {
            $q->where('academic_year_id', $selectedYearId);
            $q->whereNull('end_date'); // Only show active positions
            $q->with('position');
        }])
        ->where('employee_type', 'guru')
        ->where('school_id', $user->school_id);
        
        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }
        
        $employees = $query->where('is_active', 1)->paginate(15)->withQueryString();
        
        // Active school for reference
        $school = School::find($user->school_id);
        
        return view('treasurer.assignments.positions.index', compact(
            'employees', 
            'school', 
            'academicYears', 
            'selectedYearId',
            'semester'
        ));
    }
}
