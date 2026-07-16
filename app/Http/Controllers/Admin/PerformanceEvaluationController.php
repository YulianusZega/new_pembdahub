<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceContract;
use App\Models\PerformanceEvaluation;
use App\Models\AcademicYear;
use App\Models\Semester;

class PerformanceEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', 1)->first();
        $currentSemester = Semester::where('is_active', 1)->first();
        
        $selectedYearId = $request->input('academic_year_id', $currentYear ? $currentYear->id : null);
        
        // Filter semesters by the selected academic year to prevent duplicate/double options from all years
        $semesters = Semester::when($selectedYearId, function($q) use ($selectedYearId) {
            return $q->where('academic_year_id', $selectedYearId);
        })->orderBy('semester_number', 'asc')->orderBy('start_date', 'asc')->get();
        
        $selectedSemesterId = $request->input('semester_id', $currentSemester ? $currentSemester->id : null);
        if ($semesters->isNotEmpty() && (!$selectedSemesterId || !$semesters->contains('id', $selectedSemesterId))) {
            $selectedSemesterId = $semesters->where('is_active', 1)->first()->id ?? $semesters->first()->id;
        }
        
        $query = PerformanceContract::with(['employee', 'position', 'evaluations' => function($q) use ($selectedSemesterId) {
                $q->where('semester_id', $selectedSemesterId);
            }])
            ->where('status', PerformanceContract::STATUS_APPROVED_BY_YAYASAN)
            ->where('academic_year_id', $selectedYearId);
            
        if (!$user->isSuperAdmin() && !$user->isYayasan()) {
            $query->where('school_id', $user->school_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }
        
        $contracts = $query->paginate(15)->withQueryString();
        $selectedSemester = Semester::find($selectedSemesterId);
        
        return view('admin.performance_evaluations.index', compact(
            'contracts', 'academicYears', 'semesters', 
            'selectedYearId', 'selectedSemesterId', 'selectedSemester'
        ));
    }

    public function evaluate($contractId, $semesterId)
    {
        $user = auth()->user();
        $contract = PerformanceContract::with(['employee', 'position'])->findOrFail($contractId);
        $semester = Semester::findOrFail($semesterId);
        
        // Authorization
        if (!$user->isSuperAdmin() && !$user->isYayasan() && $contract->school_id !== $user->school_id) {
            abort(403);
        }

        $evaluation = PerformanceEvaluation::firstOrCreate(
            ['performance_contract_id' => $contract->id, 'semester_id' => $semester->id]
        );

        return view('admin.performance_evaluations.evaluate', compact('contract', 'semester', 'evaluation'));
    }

    public function store(Request $request, $contractId, $semesterId)
    {
        $user = auth()->user();
        $contract = PerformanceContract::findOrFail($contractId);
        $semester = Semester::findOrFail($semesterId);
        
        if (!$user->isSuperAdmin() && !$user->isYayasan() && $contract->school_id !== $user->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:1|max:5',
            'notes' => 'nullable|string',
            'action' => 'required|in:draft,submit_yayasan,approve_yayasan'
        ]);
        
        $totalScore = array_sum($validated['scores']);
        $count = count($validated['scores']);
        $averageScore = $count > 0 ? ($totalScore / $count) : 0;
        
        $status = PerformanceEvaluation::STATUS_DRAFT;
        if ($validated['action'] === 'submit_yayasan') {
            $status = PerformanceEvaluation::STATUS_SUBMITTED_TO_YAYASAN;
        } elseif ($validated['action'] === 'approve_yayasan') {
            if (!$user->isYayasan() && !$user->isSuperAdmin()) {
                abort(403, 'Hanya Yayasan yang dapat menyetujui evaluasi.');
            }
            $status = PerformanceEvaluation::STATUS_APPROVED_BY_YAYASAN;
        }
        
        $evaluation = PerformanceEvaluation::updateOrCreate(
            ['performance_contract_id' => $contract->id, 'semester_id' => $semester->id],
            [
                'evaluated_by' => $user->id,
                'evaluation_data' => $validated['scores'],
                'score' => $averageScore,
                'status' => $status,
                'notes' => $validated['notes'],
            ]
        );
        
        return redirect()->route('admin.performance_evaluations.index')->with('success', 'Evaluasi Kinerja berhasil disimpan.');
    }
}
