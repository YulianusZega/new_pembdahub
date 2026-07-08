<?php

namespace App\Http\Controllers;

use App\Models\CbtExamSession;
use App\Models\CbtExam;
use Illuminate\Http\Request;

class CbtExamSessionController extends Controller
{
    // List all sessions for an exam
    public function index($examId)
    {
        $sessions = CbtExamSession::where('exam_id', $examId)->get();
        return response()->json($sessions);
    }

    // Show a single session
    public function show($id)
    {
        $session = CbtExamSession::with('exam')->findOrFail($id);
        return response()->json($session);
    }

    // Create a new session
    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:cbt_exams,id',
            'session_code' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'status' => 'string',
            'is_active' => 'boolean',
        ]);
        $session = CbtExamSession::create($data);
        return response()->json($session, 201);
    }

    // Update session
    public function update(Request $request, $id)
    {
        $session = CbtExamSession::findOrFail($id);
        $data = $request->validate([
            'session_code' => 'string',
            'start_time' => 'date',
            'end_time' => 'date',
            'status' => 'string',
            'is_active' => 'boolean',
        ]);
        $session->update($data);
        return response()->json($session);
    }

    // Delete session
    public function destroy($id)
    {
        $session = CbtExamSession::findOrFail($id);
        $session->delete();
        return response()->json(['message' => 'Session deleted']);
    }
}
