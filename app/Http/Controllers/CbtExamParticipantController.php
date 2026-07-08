<?php

namespace App\Http\Controllers;

use App\Models\CbtExamParticipant;
use App\Models\CbtExamSession;
use App\Models\Student;
use Illuminate\Http\Request;

class CbtExamParticipantController extends Controller
{
    // List all participants for an exam
    public function index($examId)
    {
        $participants = CbtExamParticipant::where('exam_id', $examId)
            ->with(['student', 'session', 'answers', 'result'])
            ->get();
        return response()->json($participants);
    }

    // Show a single participant
    public function show($id)
    {
        $participant = CbtExamParticipant::with(['student', 'session', 'answers', 'result'])
            ->findOrFail($id);
        return response()->json($participant);
    }

    // Register a student for an exam
    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:cbt_exams,id',
            'student_id' => 'required|exists:students,id',
            'session_id' => 'required|exists:cbt_exam_sessions,id',
            'status' => 'string',
            'score' => 'integer',
            'start_time' => 'date',
            'end_time' => 'date',
            'duration_minutes' => 'integer',
            'answers_submitted' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $participant = CbtExamParticipant::create($data);
        return response()->json($participant, 201);
    }

    // Update participant
    public function update(Request $request, $id)
    {
        $participant = CbtExamParticipant::findOrFail($id);
        $data = $request->validate([
            'status' => 'string',
            'score' => 'integer',
            'start_time' => 'date',
            'end_time' => 'date',
            'duration_minutes' => 'integer',
            'answers_submitted' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $participant->update($data);
        return response()->json($participant);
    }

    // Delete participant
    public function destroy($id)
    {
        $participant = CbtExamParticipant::findOrFail($id);
        $participant->delete();
        return response()->json(['message' => 'Participant deleted']);
    }
}
