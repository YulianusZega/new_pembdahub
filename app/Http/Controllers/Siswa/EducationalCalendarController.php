<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\EducationalCalendarService;
use Illuminate\Http\Request;

class EducationalCalendarController extends Controller
{
    protected $calendarService;

    public function __construct(EducationalCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $school = auth()->user()->student->school ?? null;
        
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tahun Ajaran aktif belum diatur.');
        }

        if ($request->wantsJson() || $request->ajax() || $request->has('start')) {
            return response()->json($this->calendarService->getCalendarEvents($school, $academicYear));
        }

        $activeDaysGanjil = $this->calendarService->calculateActiveDaysForSemester($school, $academicYear, 1);
        $activeDaysGenap = $this->calendarService->calculateActiveDaysForSemester($school, $academicYear, 2);
        
        $activeDaysTotal = $this->calendarService->calculateActiveDays($school, $academicYear);

        return view('siswa.calendar.index', compact('academicYear', 'activeDaysGanjil', 'activeDaysGenap', 'activeDaysTotal', 'school'));
    }
}
