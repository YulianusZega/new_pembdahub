<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentDevelopmentNote;
use App\Models\StudentRecommendation;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class StudentDevelopmentController extends Controller
{
    /**
     * List development notes
     */
    public function notes(Request $request)
    {
        $query = StudentDevelopmentNote::with(['student', 'notedByUser']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('aspect')) {
            $query->where('aspect', $request->aspect);
        }

        $notes = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.counseling.notes', compact('notes'));
    }

    /**
     * Store development note
     */
    public function storeNote(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'aspect' => 'required|in:akademik,sikap,keterampilan,spiritual,sosial,fisik,ekstrakurikuler',
            'observation' => 'required|string',
            'progress' => 'nullable|string',
            'challenges' => 'nullable|string',
            'suggestion' => 'nullable|string',
            'noted_by_role' => 'required|in:guru_bk,wali_kelas,pks,kepala_sekolah,guru_mapel',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $academicYear = AcademicYear::where('is_active', true)->firstOrFail();
        $semester = Semester::where('is_active', true)->firstOrFail();

        StudentDevelopmentNote::create(array_merge($validated, [
            'school_id' => $student->school_id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'noted_by' => auth()->id(),
        ]));

        return back()->with('success', 'Catatan perkembangan berhasil disimpan.');
    }

    /**
     * List recommendations
     */
    public function recommendations(Request $request)
    {
        $query = StudentRecommendation::with(['student', 'recommendedByUser', 'counselingRecord']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $recommendations = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.counseling.recommendations', compact('recommendations'));
    }

    /**
     * Store recommendation
     */
    public function storeRecommendation(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counseling_record_id' => 'nullable|exists:student_counseling_records,id',
            'recommender_role' => 'required|in:guru_bk,wali_kelas,pks,kepala_sekolah,guru_mapel',
            'category' => 'required|in:akademik,perilaku,bakat,karir,kesehatan,sosial,lainnya',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expected_outcome' => 'nullable|string',
            'priority' => 'in:rendah,sedang,tinggi',
            'target_date' => 'nullable|date',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $academicYear = AcademicYear::where('is_active', true)->firstOrFail();
        $semester = Semester::where('is_active', true)->firstOrFail();

        StudentRecommendation::create(array_merge($validated, [
            'school_id' => $student->school_id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'recommended_by' => auth()->id(),
            'status' => 'pending',
        ]));

        return back()->with('success', 'Rekomendasi berhasil disimpan.');
    }

    /**
     * Update recommendation status
     */
    public function updateRecommendation(Request $request, StudentRecommendation $recommendation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,completed,rejected',
            'action_result' => 'nullable|string',
        ]);

        $recommendation->update($validated);

        return back()->with('success', 'Status rekomendasi berhasil diperbarui.');
    }

    /**
     * Student development profile - all records for one student
     */
    public function studentProfile(Student $student)
    {
        $counselingRecords = $student->counselingRecords()
            ->with('counselor', 'participants.user')
            ->orderByDesc('incident_date')
            ->get();

        $developmentNotes = $student->developmentNotes()
            ->with('notedByUser')
            ->orderByDesc('created_at')
            ->get();

        $recommendations = $student->recommendations()
            ->with('recommendedByUser')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.counseling.student-profile', compact(
            'student', 'counselingRecords', 'developmentNotes', 'recommendations'
        ));
    }
}
