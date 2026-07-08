<?php

namespace App\Http\Controllers;

use App\Models\CbtAnswer;
use Illuminate\Http\Request;

class CbtAnswerController extends Controller
{
    // List all answers for a participant
    public function index($participantId)
    {
        $answers = CbtAnswer::where('participant_id', $participantId)
            ->with(['question', 'option'])
            ->get();
        return response()->json($answers);
    }

    // Show a single answer
    public function show($id)
    {
        $answer = CbtAnswer::with(['question', 'option'])->findOrFail($id);
        return response()->json($answer);
    }

    // Create a new answer
    public function store(Request $request)
    {
        $data = $request->validate([
            'participant_id' => 'required|exists:cbt_exam_participants,id',
            'question_id' => 'required|exists:cbt_questions,id',
            'option_id' => 'nullable|exists:cbt_question_options,id',
            'answer_text' => 'nullable|string',
            'answer_image' => 'nullable|string',
            'is_correct' => 'boolean',
            'score' => 'integer',
            'answered_at' => 'date',
        ]);
        $answer = CbtAnswer::create($data);
        return response()->json($answer, 201);
    }

    // Update answer
    public function update(Request $request, $id)
    {
        $answer = CbtAnswer::findOrFail($id);
        $data = $request->validate([
            'option_id' => 'nullable|exists:cbt_question_options,id',
            'answer_text' => 'nullable|string',
            'answer_image' => 'nullable|string',
            'is_correct' => 'boolean',
            'score' => 'integer',
            'answered_at' => 'date',
        ]);
        $answer->update($data);
        return response()->json($answer);
    }

    // Delete answer
    public function destroy($id)
    {
        $answer = CbtAnswer::findOrFail($id);
        $answer->delete();
        return response()->json(['message' => 'Answer deleted']);
    }
}
