<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Repositories\StudentRepository;
use App\Services\StudentService;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentSampleExport;

class StudentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private StudentRepository $studentRepository,
        private StudentService $studentService
    ) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Get active academic year for defaults
        $activeAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();

        // Determine selected academic year - default to active year
        $selectedAcademicYearId = $request->filled('academic_year_id')
            ? $request->academic_year_id
            : ($activeAcademicYear?->id ?? null);

        $filters = $request->only(['q', 'school_id', 'status', 'classroom_id']);
        $filters['academic_year_id'] = $selectedAcademicYearId;
        $students = $this->studentRepository->getFilteredPaginated($filters);

        $schools = \App\Models\School::getActiveCached();
        
        // Load classrooms filtered by selected academic year
        $classroomQuery = \App\Models\Classroom::where('is_active', true);
        if ($selectedAcademicYearId) {
            $classroomQuery->where('academic_year_id', $selectedAcademicYearId);
        }
        if (!auth()->user()->isSuperAdmin()) {
            $classroomQuery->where('school_id', auth()->user()->school_id);
        } elseif ($request->filled('school_id')) {
            $classroomQuery->where('school_id', $request->school_id);
        }
        $classrooms = $classroomQuery->orderBy('class_name')->get();

        return view('admin.students.index', compact('students', 'schools', 'classrooms', 'academicYears', 'activeAcademicYear', 'selectedAcademicYearId'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);

        $schools = \App\Models\School::getActiveCached();
        return view('admin.students.create', compact('schools'));
    }

    public function store(StoreStudentRequest $request)
    {
        $this->authorize('create', Student::class);

        try {
            $this->studentService->createStudentWithUser(
                $request->validated(),
                $request->file('photo')
            );

            return redirect()->route('admin.students.index')
                ->with('success', 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal membuat siswa: ' . $e->getMessage());
            return back()->withErrors(['student' => 'Gagal membuat siswa. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);

        $student = $this->studentRepository->findWithRelations($student->id);
        
        // Calculate Attendance Stats
        $attendanceRepo = app(\App\Repositories\AttendanceRepository::class);
        $attendanceStats = $attendanceRepo->getStatistics($student->id);

        return view('admin.students.show', compact('student', 'attendanceStats'));
    }

    /**
     * Show payment history timeline for a student
     */
    public function paymentHistory(Student $student)
    {
        $this->authorize('view', $student);

        // Get all payments for this student with bill details
        $payments = \App\Models\Payment::where('student_id', $student->id)
            ->with(['bill.paymentType', 'processedBy'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20)->withQueryString();

        // Get all bills for this student
        $bills = \App\Models\StudentBill::where('student_id', $student->id)
            ->with(['paymentType', 'academicYear'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Calculate statistics
        $totalBilled = $bills->sum('amount');
        $totalPaid = $bills->sum('paid_amount');
        $totalOutstanding = $totalBilled - $totalPaid;
        $totalLateFees = $bills->sum('late_fee');

        return view('admin.students.payment-history', compact(
            'student',
            'payments',
            'bills',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'totalLateFees'
        ));
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);

        $schools = \App\Models\School::getActiveCached();
        return view('admin.students.edit', compact('student', 'schools'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->authorize('update', $student);

        try {
            $this->studentService->updateStudent(
                $student,
                $request->validated(),
                $request->file('photo'),
                $request->boolean('remove_photo')
            );

            return redirect()->route('admin.students.index')
                ->with('success', 'Siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui siswa: ' . $e->getMessage());
            return back()->withErrors(['student' => 'Gagal memperbarui siswa. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        try {
            $this->studentService->deleteStudent($student);
            return redirect()->route('admin.students.index')
                ->with('success', 'Siswa berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus siswa: ' . $e->getMessage());
            return back()->withErrors(['student' => 'Gagal menghapus siswa. Silakan coba lagi.']);
        }
    }

    public function updateRfid(Request $request, Student $student)
    {
        $this->authorize('update', $student);
        
        $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:students,rfid_uid,' . $student->id
        ], [
            'rfid_uid.unique' => 'Kartu RFID ini sudah terdaftar untuk siswa lain.'
        ]);

        $student->update([
            'rfid_uid' => strtoupper(trim($request->rfid_uid))
        ]);

        return redirect()->back()->with('success', "RFID UID berhasil didaftarkan untuk siswa {$student->full_name}.");
    }

    /**
     * Import students from CSV (minimal implementation)
     */
    public function importForm()
    {
        $this->authorize('import', Student::class);

        return view('admin.students.import');
    }

    public function import(Request $request)
    {
        $this->authorize('import', Student::class);
        $request->validate([
            'file' => 'nullable|file|mimes:xlsx,xls',
            'csv' => 'nullable|file|mimes:xlsx,xls,csv,txt',
        ]);

        $file = $request->file('file') ?? $request->file('csv');

        if (!$file) {
            return back()->withErrors(['file' => 'File import wajib diunggah.']);
        }

        try {
            $extension = $file->getClientOriginalExtension();
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                $import = new StudentsImport();
                Excel::import($import, $file);
                $rows = $import->getRows();
            } else {
                $path = $file->getRealPath();
                $handle = fopen($path, 'r');
                $header = fgetcsv($handle);

                $rows = [];
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = array_combine($header, $row);
                }
                fclose($handle);
            }

            $result = $this->studentService->importStudents($rows);

            if ($result['failed'] > 0) {
                $errorMsg = "Import selesai dengan " . $result['failed'] . " error. ";
                $errorMsg .= implode('; ', array_slice($result['errors'], 0, 5));

                return redirect()->route('admin.students.index')
                    ->with('warning', $errorMsg)
                    ->with('success', "{$result['imported']} siswa berhasil diimpor.");
            }

            return redirect()->route('admin.students.index')
                ->with('success', "{$result['imported']} siswa berhasil diimpor.");
        } catch (\Exception $e) {
            Log::error('Gagal mengimpor siswa: ' . $e->getMessage());
            return back()->withErrors(['file' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage()]);
        }
    }

    /**
     * Download a sample Excel template for imports
     */
    public function downloadSampleExcel()
    {
        return Excel::download(new StudentSampleExport, 'sample_students.xlsx');
    }
}
