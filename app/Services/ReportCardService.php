<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Grade;
use App\Models\GradeWeight;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAchievement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportCardService
{
    public function __construct(
        protected GradeService $gradeService,
    ) {}

    // ──────────────────────────────────────────────
    //  Report Card Generation
    // ──────────────────────────────────────────────

    /**
     * Generate report cards for a list of students.
     *
     * @return array{generated: int, updated: int}
     */
    public function generateReportCards(Collection $students, int $semesterId, int $academicYearId): array
    {
        $generated = 0;
        $updated = 0;

        $semester = Semester::with('academicYear')->findOrFail($semesterId);

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $studentClass = $student->studentClasses()
                    ->where('status', 'aktif')
                    ->where('academic_year_id', $academicYearId)
                    ->first();

                if (!$studentClass) {
                    continue;
                }

                $classroomId = $studentClass->classroom_id;

                // Calculate weighted average score
                $reportData = $this->gradeService->getStudentReportCard($student->id, $semesterId, $student->school_id);

                $avgScore = 0;
                $predicate = 'D';
                if (!empty($reportData['subjects'])) {
                    $avgScore = $reportData['overall_average'];
                    $predicate = $reportData['overall_predicate'];

                    $this->gradeService->calculateAndSaveFinalGrades($student->id, $semesterId, $student->school_id);
                }

                $attendanceData = $this->calculateAttendance($student->id, $semester);

                $reportCard = ReportCard::where('student_id', $student->id)
                    ->where('semester_id', $semesterId)
                    ->first();

                $data = [
                    'student_id' => $student->id,
                    'semester_id' => $semesterId,
                    'academic_year_id' => $academicYearId,
                    'classroom_id' => $classroomId,
                    'average_score' => round($avgScore, 2),
                    'predicate' => $predicate,
                    'total_days' => $attendanceData['total_days'],
                    'days_present' => $attendanceData['present'],
                    'days_sick' => $attendanceData['sick'],
                    'days_permission' => $attendanceData['permission'],
                    'days_absent' => $attendanceData['absent'],
                ];

                if ($reportCard) {
                    if ($reportCard->status === 'draft') {
                        $reportCard->update($data);
                        $updated++;
                    }
                } else {
                    ReportCard::create($data);
                    $generated++;
                }
            }

            $this->calculateRanks($semesterId);

            DB::commit();

            return compact('generated', 'updated');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ──────────────────────────────────────────────
    //  Subject Scores (DRY — used by show, print, bulkDownload)
    // ──────────────────────────────────────────────

    /**
     * Build per-subject score data for a report card.
     */
    public function buildSubjectScores(ReportCard $reportCard): array
    {
        $grades = Grade::with(['subject'])
            ->where('student_id', $reportCard->student_id)
            ->where('semester_id', $reportCard->semester_id)
            ->get()
            ->groupBy('subject_id');

        $gradeWeight = GradeWeight::getForSchool($reportCard->student->school_id);
        $w = $gradeWeight->getWeightsAsDecimal();

        if (!$reportCard->relationLoaded('classroom')) {
            $reportCard->load('classroom');
        }
        $gradeLevel = $reportCard->classroom?->grade_level;

        $subjectScores = [];
        foreach ($grades as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;

            $tugas = $subjectGrades->where('grade_type', 'tugas')->avg('score') ?? 0;
            $uts   = $subjectGrades->where('grade_type', 'uts')->avg('score') ?? 0;
            $uas   = $subjectGrades->where('grade_type', 'uas')->avg('score') ?? 0;
            $sikap = $subjectGrades->where('grade_type', 'sikap')->avg('score') ?? 0;

            $finalScore = ($tugas * $w['tugas']) + ($uts * $w['pts']) + ($uas * $w['pas']) + ($sikap * $w['sikap']);
            $kkm = $subject->kkm ?? 75;

            $subjectScores[] = [
                'subject'   => $subject->subject_name,
                'kkm'       => $kkm,
                'tugas'     => round($tugas, 0),
                'uts'       => round($uts, 0),
                'uas'       => round($uas, 0),
                'sikap'     => round($sikap, 0),
                'final'     => round($finalScore, 0),
                'predicate' => $this->scoreToPredicate($finalScore, $kkm, $gradeLevel),
                'is_passed' => $finalScore >= $kkm,
            ];
        }

        return $subjectScores;
    }

    /**
     * Get achievements for a student in a given academic year.
     */
    public function getAchievements(int $studentId, int $academicYearId): Collection
    {
        return StudentAchievement::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->orderByDesc('level')
            ->get();
    }

    // ──────────────────────────────────────────────
    //  Bulk PDF ZIP Download
    // ──────────────────────────────────────────────

    /**
     * Generate a ZIP file containing PDFs for all report cards in a class.
     *
     * @return string  Absolute path to the temporary ZIP file.
     * @throws \Exception
     */
    public function buildBulkDownloadZip(Collection $reportCards): string
    {
        $tempZipPath = storage_path('app/temp/' . uniqid('rapor_') . '.zip');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Gagal membuat file ZIP.');
        }

        try {
            foreach ($reportCards as $reportCard) {
                $subjectScores = $this->buildSubjectScores($reportCard);

                $achievements = StudentAchievement::where('student_id', $reportCard->student_id)
                    ->where('academic_year_id', $reportCard->academic_year_id)
                    ->orderByDesc('level')
                    ->get();

                $pdf = Pdf::loadView('admin.report_cards.pdf', compact('reportCard', 'subjectScores', 'achievements'));

                $pdfFilename = 'Rapor_'
                    . str_pad($reportCard->rank ?? 0, 2, '0', STR_PAD_LEFT)
                    . '_' . $reportCard->student->full_name . '.pdf';

                $zip->addFromString($pdfFilename, $pdf->output());
            }

            $zip->close();

            return $tempZipPath;
        } catch (\Exception $e) {
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
            throw $e;
        }
    }

    // ──────────────────────────────────────────────
    //  Helper Methods
    // ──────────────────────────────────────────────

    /**
     * Calculate attendance summary for a student in a semester.
     */
    public function calculateAttendance(int $studentId, Semester $semester): array
    {
        $attendances = Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$semester->start_date, $semester->end_date])
            ->get();

        return [
            'total_days'  => $attendances->count(),
            'present'     => $attendances->where('status', 'hadir')->count(),
            'sick'        => $attendances->where('status', 'sakit')->count(),
            'permission'  => $attendances->where('status', 'izin')->count(),
            'absent'      => $attendances->where('status', 'alpha')->count(),
        ];
    }

    /**
     * Calculate and update ranks per classroom for a semester.
     */
    public function calculateRanks(int $semesterId): void
    {
        $reportCards = ReportCard::where('semester_id', $semesterId)
            ->orderBy('classroom_id')
            ->orderBy('average_score', 'desc')
            ->get()
            ->groupBy('classroom_id');

        foreach ($reportCards as $classroomId => $classReports) {
            $rank = 1;
            $totalStudents = $classReports->count();

            foreach ($classReports as $report) {
                $report->update([
                    'rank' => $rank,
                    'total_students' => $totalStudents,
                ]);
                $rank++;
            }
        }
    }

    /**
     * Convert numeric score to letter predicate.
     */
    public function scoreToPredicate(float $score, int $kkm = 75, ?int $gradeLevel = null): string
    {
        return \App\Models\FinalGrade::scoreToPredicate($score, $kkm, $gradeLevel);
    }
}
