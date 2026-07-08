<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Classroom;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReportCardController extends Controller
{
    public function __construct(
        protected ReportCardService $reportCardService,
    ) {}

    // ──────────────────────────────────────────────
    //  Authorization Helpers (kept in controller — HTTP layer concern)
    // ──────────────────────────────────────────────

    private function authorizeAccess()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['superadmin', 'admin_sekolah'])) {
            return true;
        }

        if ($user->isGuru() && $user->isHomeroomTeacher()) {
            return true;
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    private function isHomeroomTeacherOnly()
    {
        $user = auth()->user();
        return $user->isGuru() && $user->isHomeroomTeacher() && !$user->hasAnyRole(['superadmin', 'admin_sekolah']);
    }

    private function authorizeReportCard(ReportCard $reportCard)
    {
        if ($this->isHomeroomTeacherOnly()) {
            $user = auth()->user();
            $homeroomClassroomIds = $user->homeroomClassrooms()->pluck('id')->toArray();

            if (!in_array($reportCard->classroom_id, $homeroomClassroomIds)) {
                abort(403, 'Anda hanya dapat mengelola rapor dari kelas yang Anda ampu.');
            }
        }
    }

    // ──────────────────────────────────────────────
    //  CRUD
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $user = auth()->user();
        $schoolId = $user->role === 'superadmin' ? null : $user->school_id;

        $semesterId = $request->filled('semester_id')
            ? $request->semester_id
            : (Semester::where('is_active', true)->first()?->id
               ?? Semester::orderBy('id', 'desc')->first()?->id);

        $classroomId = $request->input('classroom_id');
        $status = $request->input('status');

        $query = ReportCard::with(['student', 'semester', 'academicYear', 'classroom'])
            ->bySemester($semesterId);

        if ($schoolId) {
            $query->whereHas('student', fn($q) => $q->where('school_id', $schoolId));
        }

        if ($this->isHomeroomTeacherOnly()) {
            $homeroomClassroomIds = $user->homeroomClassrooms()->pluck('id');
            $query->whereIn('classroom_id', $homeroomClassroomIds);
        }

        if ($classroomId) {
            $query->byClassroom($classroomId);
        }
        if ($status) {
            $query->byStatus($status);
        }

        $reportCards = $query->orderBy('classroom_id')->orderBy('rank')->paginate(20)->withQueryString();

        $semesters = Cache::remember('report_card_semesters', 3600, function () {
            return Semester::with('academicYear')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get()
                ->unique(fn($s) => $s->academic_year_id . '-' . $s->semester_name)
                ->values();
        });

        $classrooms = Classroom::query();
        if ($schoolId) {
            $classrooms->where('school_id', $schoolId);
        }
        $selectedSemester = Semester::find($semesterId);
        if ($selectedSemester) {
            $classrooms->where('academic_year_id', $selectedSemester->academic_year_id);
        }
        $classrooms = $classrooms->orderBy('class_name')->get();

        if ($semesterId) {
            $statusCounts = ReportCard::where('semester_id', $semesterId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $stats = [
                'total' => $statusCounts->sum(),
                'draft' => $statusCounts->get('draft', 0),
                'finalized' => $statusCounts->get('finalized', 0),
                'published' => $statusCounts->get('published', 0),
            ];
        } else {
            $stats = ['total' => 0, 'draft' => 0, 'finalized' => 0, 'published' => 0];
        }

        return view('admin.report_cards.index', compact(
            'reportCards', 'semesters', 'classrooms', 'semesterId', 'classroomId', 'status', 'stats'
        ));
    }

    public function generate(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $semesterId = $validated['semester_id'];
        $classroomId = $validated['classroom_id'] ?? null;

        // Homeroom teacher restriction
        if ($this->isHomeroomTeacherOnly()) {
            $homeroomClassroomIds = auth()->user()->homeroomClassrooms()->pluck('id');

            if ($classroomId && !$homeroomClassroomIds->contains($classroomId)) {
                return redirect()->back()->with('error', 'Anda hanya bisa generate rapor untuk kelas yang Anda ampu.');
            }

            if (!$classroomId) {
                $classroomId = $homeroomClassroomIds->first();
            }
        }

        $semester = Semester::with('academicYear')->findOrFail($semesterId);
        $academicYearId = $semester->academic_year_id;

        // Build students query
        $studentsQuery = Student::where('status', 'aktif');

        if ($classroomId) {
            $studentsQuery->whereHas('studentClasses', function ($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId)->where('status', 'aktif');
            });
        }

        if (auth()->user()->role !== 'superadmin') {
            $studentsQuery->where('school_id', auth()->user()->school_id);
        }

        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa aktif untuk generate rapor.');
        }

        try {
            $result = $this->reportCardService->generateReportCards($students, $semesterId, $academicYearId);

            $message = "Berhasil generate {$result['generated']} rapor baru";
            if ($result['updated'] > 0) {
                $message .= " dan update {$result['updated']} rapor draft";
            }

            return redirect()->route('admin.report_cards.index', ['semester_id' => $semesterId])
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Gagal generate rapor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate rapor. Silakan coba lagi.');
        }
    }

    public function show(ReportCard $reportCard)
    {
        $this->authorizeAccess();

        $reportCard->load(['student.school', 'semester.academicYear', 'classroom', 'finalizedBy', 'publishedBy']);

        $subjectScores = $this->reportCardService->buildSubjectScores($reportCard);
        $achievements = $this->reportCardService->getAchievements($reportCard->student_id, $reportCard->academic_year_id);

        return view('admin.report_cards.show', compact('reportCard', 'subjectScores', 'achievements'));
    }

    public function edit(ReportCard $reportCard)
    {
        $this->authorizeAccess();

        if ($this->isHomeroomTeacherOnly()) {
            $homeroomClassroomIds = auth()->user()->homeroomClassrooms()->pluck('id');
            if (!$homeroomClassroomIds->contains($reportCard->classroom_id)) {
                abort(403, 'Anda hanya bisa edit rapor di kelas yang Anda ampu.');
            }
        }

        if (!$reportCard->isEditable()) {
            return redirect()->back()->with('error', 'Rapor yang sudah finalized/published tidak bisa diedit.');
        }

        $reportCard->load(['student', 'semester', 'classroom']);

        return view('admin.report_cards.edit', compact('reportCard'));
    }

    public function update(Request $request, ReportCard $reportCard)
    {
        $this->authorizeAccess();

        if ($this->isHomeroomTeacherOnly()) {
            $homeroomClassroomIds = auth()->user()->homeroomClassrooms()->pluck('id');
            if (!$homeroomClassroomIds->contains($reportCard->classroom_id)) {
                abort(403, 'Anda hanya bisa edit rapor di kelas yang Anda ampu.');
            }
        }

        if (!$reportCard->isEditable()) {
            return redirect()->back()->with('error', 'Rapor yang sudah finalized/published tidak bisa diedit.');
        }

        if ($this->isHomeroomTeacherOnly()) {
            $validated = $request->validate([
                'teacher_notes' => 'nullable|string|max:1000',
            ]);
        } else {
            $validated = $request->validate([
                'teacher_notes' => 'nullable|string|max:1000',
                'principal_notes' => 'nullable|string|max:1000',
                'achievements' => 'nullable|string|max:1000',
                'recommendations' => 'nullable|string|max:1000',
            ]);
        }

        $reportCard->update($validated);

        return redirect()->route('admin.report_cards.show', $reportCard)
            ->with('success', 'Rapor berhasil diupdate.');
    }

    public function finalize(ReportCard $reportCard)
    {
        $this->authorizeAccess();
        $this->authorizeReportCard($reportCard);

        if ($reportCard->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya rapor draft yang bisa difinalize.');
        }

        $reportCard->update([
            'status' => 'finalized',
            'finalized_by' => auth()->id(),
            'finalized_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Rapor berhasil difinalize.');
    }

    public function publish(ReportCard $reportCard)
    {
        $this->authorizeAccess();
        $this->authorizeReportCard($reportCard);

        if ($reportCard->status !== 'finalized') {
            return redirect()->back()->with('error', 'Hanya rapor finalized yang bisa dipublish.');
        }

        $reportCard->update([
            'status' => 'published',
            'published_by' => auth()->id(),
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Rapor berhasil dipublish.');
    }

    public function print(ReportCard $reportCard)
    {
        $this->authorizeAccess();

        $reportCard->load(['student.school', 'semester.academicYear', 'classroom']);

        $subjectScores = $this->reportCardService->buildSubjectScores($reportCard);
        $achievements = $this->reportCardService->getAchievements($reportCard->student_id, $reportCard->academic_year_id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.report_cards.pdf', compact('reportCard', 'subjectScores', 'achievements'));

        $filename = 'Rapor_' . $reportCard->student->full_name . '_' . $reportCard->semester->semester_name . '.pdf';

        return $pdf->download($filename);
    }

    public function bulkDownload(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $semester = Semester::with('academicYear')->findOrFail($validated['semester_id']);
        $classroom = Classroom::with('school')->findOrFail($validated['classroom_id']);

        if ($this->isHomeroomTeacherOnly()) {
            $homeroomClassroomIds = auth()->user()->homeroomClassrooms()->pluck('id');
            if (!$homeroomClassroomIds->contains($classroom->id)) {
                return redirect()->back()->with('error', 'Anda hanya bisa download rapor dari kelas yang Anda ampu.');
            }
        }

        $reportCards = ReportCard::with(['student.school', 'semester.academicYear', 'classroom'])
            ->where('semester_id', $semester->id)
            ->where('classroom_id', $classroom->id)
            ->orderBy('rank')
            ->get();

        if ($reportCards->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada rapor untuk kelas dan semester ini.');
        }

        $zipFilename = 'Rapor_' . $classroom->class_name . '_' . ($semester->semester_name ?? '') . '.zip';

        try {
            $tempZipPath = $this->reportCardService->buildBulkDownloadZip($reportCards);
            return response()->download($tempZipPath, $zipFilename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Bulk download rapor gagal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat bulk download. ' . $e->getMessage());
        }
    }
}
