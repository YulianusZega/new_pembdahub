<?php

namespace App\Repositories;

use App\Models\Grade;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository
{
    /**
     * Get paginated grades with relationships and filters
     */
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Grade::with([
            'student:id,nisn,full_name,school_id',
            'subject:id,subject_name',
            'teacher:id,full_name',
            'semester:id,semester_name,academic_year_id',
            'semester.academicYear:id,year',
            'classroom'
        ]);

        if (!empty($filters['academic_year_id'])) {
            $query->whereHas('semester', function($q) use ($filters) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            });
        }

        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }

        // Auto-filter by school_id for non-superadmin
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin()) {
            $query->whereHas('student', function($q) use ($user) {
                $q->where('school_id', $user->school_id);
            });
        } elseif (!empty($filters['school_id'])) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->where('school_id', $filters['school_id']);
            });
        }

        if (!empty($filters['classroom_id'])) {
            $query->whereHas('student', function($q) use ($filters) {
                $q->whereHas('studentClasses', function($sq) use ($filters) {
                    $sq->where('classroom_id', $filters['classroom_id'])
                      ->where('status', 'aktif');
                });
            });
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        return $query->select('grades.id', 'grades.student_id', 'grades.subject_id', 'grades.teacher_id', 'grades.semester_id', 'grades.grade_type', 'grades.score', 'grades.notes', 'grades.lms_source_type', 'grades.lms_source_id')
            ->latest('grades.id')
            ->paginate($perPage);
    }

    /**
     * Get grades by student
     */
    public function getByStudent(int $studentId, ?int $semesterId = null): Collection
    {
        $query = Grade::with(['subject:id,subject_name', 'teacher:id,full_name'])
            ->where('student_id', $studentId);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return $query->orderBy('subject_id')->get();
    }

    /**
     * Get grades by subject and semester
     */
    public function getBySubjectAndSemester(int $subjectId, int $semesterId): Collection
    {
        return Grade::with('student:id,nisn,full_name')
            ->where('subject_id', $subjectId)
            ->where('semester_id', $semesterId)
            ->orderBy('student_id')
            ->get();
    }

    /**
     * Get average score for student
     */
    public function getStudentAverage(int $studentId, ?int $semesterId = null): float
    {
        $query = Grade::where('student_id', $studentId);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return (float) $query->avg('score') ?? 0;
    }

    /**
     * Create grade
     */
    public function create(array $data): Grade
    {
        return Grade::create($data);
    }

    /**
     * Update grade
     */
    public function update(Grade $grade, array $data): bool
    {
        return $grade->update($data);
    }

    /**
     * Delete grade
     */
    public function delete(Grade $grade): bool
    {
        return $grade->delete();
    }

    /**
     * Check if grade exists for student, subject, semester, and type
     */
    public function exists(int $studentId, int $subjectId, int $semesterId, string $gradeType, ?int $excludeId = null): bool
    {
        $query = Grade::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('semester_id', $semesterId)
            ->where('grade_type', $gradeType);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
