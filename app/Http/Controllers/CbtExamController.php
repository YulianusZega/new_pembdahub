<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\CbtExamParticipant;
use App\Models\CbtExamSession;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtAnswer;
use App\Models\CbtExamResult;
use Illuminate\Http\Request;

class CbtExamController extends Controller
{
    // List all CBT exams
    public function index()
    {
        $exams = CbtExam::with(['questionBank', 'subject', 'classroom', 'academicYear', 'semester'])->get();
        return response()->json($exams);
    }

    // Show a single CBT exam
    public function show($id)
    {
        $exam = CbtExam::with(['questionBank', 'subject', 'classroom', 'academicYear', 'semester', 'sessions', 'participants'])->findOrFail($id);
        return response()->json($exam);
    }

    // Create a new CBT exam
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_bank_id' => 'required|exists:cbt_question_banks,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'duration_minutes' => 'required|integer',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'status' => 'string',
            'max_score' => 'integer',
            'min_score' => 'integer',
            'passing_score' => 'integer',
            'is_active' => 'boolean',
        ]);
        $exam = CbtExam::create($data);
        return response()->json($exam, 201);
    }

    // Update a CBT exam
    public function update(Request $request, $id)
    {
        $exam = CbtExam::findOrFail($id);
        $data = $request->validate([
            'title' => 'string',
            'description' => 'nullable|string',
            'start_time' => 'date',
            'end_time' => 'date',
            'duration_minutes' => 'integer',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'status' => 'string',
            'max_score' => 'integer',
            'min_score' => 'integer',
            'passing_score' => 'integer',
            'is_active' => 'boolean',
        ]);
        $exam->update($data);
        return response()->json($exam);
    }

    // Delete a CBT exam
    public function destroy($id)
    {
        $exam = CbtExam::findOrFail($id);
        $exam->delete();
        return response()->json(['message' => 'Exam deleted']);
    }
}
