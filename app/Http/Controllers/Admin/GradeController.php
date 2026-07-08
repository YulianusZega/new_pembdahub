<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Repositories\GradeRepository;
use App\Services\GradeService;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private GradeRepository $gradeRepository,
        private GradeService $gradeService
    ) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Grade::class);

        $filters = $request->only(['academic_year_id', 'semester_id', 'school_id', 'classroom_id', 'subject_id']);
        
        if (!$request->has('academic_year_id')) {
            $filters['academic_year_id'] = \App\Models\AcademicYear::where('is_active', true)->first()?->id;
        }
        
        $grades = $this->gradeRepository->getPaginated($filters);
        
        $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
        $semesters = \App\Models\Semester::with('academicYear')->orderBy('id', 'desc')->get();
        $schools = \App\Models\School::where('is_active', true)->schoolsOnly()->orderBy('name')->get();
        
        $academicYearId = $filters['academic_year_id'] ?? null;
        $classrooms = Classroom::when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->when(!empty($filters['school_id']), function ($q) use ($filters) {
                $q->where('school_id', $filters['school_id']);
            })
            ->orderBy('class_name')
            ->get();

        return view('admin.grades.index', compact('grades', 'academicYears', 'semesters', 'schools', 'classrooms', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', Grade::class);

        $activeAY = \App\Models\AcademicYear::where('is_active', true)->first();
        $classrooms = Classroom::when($activeAY, fn($q) => $q->where('academic_year_id', $activeAY->id))
            ->orderBy('class_name')
            ->get();
        $classroomId = request('classroom_id');
        $subjectId = request('subject_id');

        $subjects = collect();
        $students = collect();
        $teachers = collect();
        if ($classroomId) {
            $classroom = Classroom::find($classroomId);
            $subjects = Subject::where('school_id', $classroom?->school_id)->get();
            $students = \App\Models\StudentClass::where('classroom_id', $classroomId)
                ->with('student')
                ->get()
                ->pluck('student');
            $teachers = \App\Models\Teacher::where('school_id', $classroom?->school_id)->get();
        }
        return view('admin.grades.create', compact('students', 'subjects', 'teachers', 'classrooms', 'classroomId', 'subjectId'));
    }

    public function store(StoreGradeRequest $request)
    {
        $this->authorize('create', Grade::class);
        try {
            $this->gradeService->createGrade($request->validated());

            return redirect()->route('admin.grades.index')
                ->with('success', 'Nilai berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan nilai: ' . $e->getMessage());
            return back()->withErrors(['grade' => 'Gagal menambahkan nilai. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function edit(Grade $grade)
    {
        $this->authorize('update', $grade);

        $classroom = Classroom::find($grade->classroom_id);
        $academicYearId = $classroom?->academic_year_id ?? \App\Models\AcademicYear::where('is_active', true)->first()?->id;
        $schoolId = $classroom?->school_id;

        // Hanya muat siswa yang ada di kelas ini (bukan Student::all())
        $students = Student::whereHas('studentClasses', function ($q) use ($grade, $academicYearId) {
                $q->where('classroom_id', $grade->classroom_id)
                  ->whereIn('status', ['aktif', 'enrolled', 'active']);
                if ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                }
            })
            ->orderBy('full_name')
            ->get();

        // Hanya muat mapel di sekolah yang sama (bukan Subject::all())
        $subjects = $schoolId
            ? Subject::where('school_id', $schoolId)->orderBy('subject_name')->get()
            : Subject::orderBy('subject_name')->limit(200)->get();

        $classrooms = Classroom::where('academic_year_id', $academicYearId)
            ->orderBy('class_name')
            ->get();

        return view('admin.grades.edit', compact('grade', 'students', 'subjects', 'classrooms'));
    }

    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        $this->authorize('update', $grade);

        try {
            $this->gradeService->updateGrade($grade, $request->validated());

            return redirect()->route('admin.grades.index')
                ->with('success', 'Nilai berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate nilai: ' . $e->getMessage());
            return back()->withErrors(['grade' => 'Gagal mengupdate nilai. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function destroy(Grade $grade)
    {
        $this->authorize('delete', $grade);
        try {
            $this->gradeService->deleteGrade($grade);
            return redirect()->route('admin.grades.index')
                ->with('success', 'Nilai berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus nilai: ' . $e->getMessage());
            return back()->withErrors(['grade' => 'Gagal menghapus nilai. Silakan coba lagi.']);
        }
    }
}
