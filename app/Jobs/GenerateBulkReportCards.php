<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateBulkReportCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * Timeout in seconds (PDF generation can be slow).
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param int $classroomId
     * @param int $academicYearId
     * @param int $userId  The admin user who triggered the generation
     */
    public function __construct(
        protected int $classroomId,
        protected int $academicYearId,
        protected int $userId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $classroom = \App\Models\Classroom::with('school')->findOrFail($this->classroomId);

            $students = \App\Models\StudentClass::where('classroom_id', $this->classroomId)
                ->where('academic_year_id', $this->academicYearId)
                ->where('status', 'aktif')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter();

            if ($students->isEmpty()) {
                Log::warning('Bulk report card generation: no students found', [
                    'classroom_id' => $this->classroomId,
                    'academic_year_id' => $this->academicYearId,
                ]);
                return;
            }

            $reportDir = 'reports/report_cards/' . now()->format('Y-m-d');
            Storage::disk('local')->makeDirectory($reportDir);

            foreach ($students as $student) {
                try {
                    $grades = \App\Models\Grade::where('student_id', $student->id)
                        ->where('academic_year_id', $this->academicYearId)
                        ->with('subject')
                        ->get();

                    $pdf = Pdf::loadView('admin.report_cards.pdf', [
                        'student' => $student,
                        'classroom' => $classroom,
                        'grades' => $grades,
                        'school' => $classroom->school,
                    ]);

                    $filename = "{$reportDir}/raport_{$student->nis}_{$student->full_name}.pdf";
                    Storage::disk('local')->put($filename, $pdf->output());
                } catch (\Exception $e) {
                    Log::error("Failed to generate report card for student {$student->id}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Bulk report card generation completed', [
                'classroom_id' => $this->classroomId,
                'student_count' => $students->count(),
                'directory' => $reportDir,
                'triggered_by' => $this->userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk report card generation failed', [
                'classroom_id' => $this->classroomId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::critical('Bulk report card generation permanently failed', [
            'classroom_id' => $this->classroomId,
            'academic_year_id' => $this->academicYearId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
