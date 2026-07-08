<?php

namespace App\Http\Controllers;

use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use Illuminate\Http\Request;

class CbtQuestionController extends Controller
{
    // List all questions for a bank
    public function index($bankId)
    {
        $questions = CbtQuestion::where('question_bank_id', $bankId)
            ->with(['options'])
            ->get();
        return response()->json($questions);
    }

    // Show a single question
    public function show($id)
    {
        $question = CbtQuestion::with(['options'])->findOrFail($id);
        return response()->json($question);
    }

    // Create a new question
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_bank_id' => 'required|exists:cbt_question_banks,id',
            'question_type' => 'required|string',
            'question_text' => 'required|string',
            'question_image' => 'nullable|string',
            'explanation' => 'nullable|string',
            'points' => 'integer',
            'difficulty' => 'string',
            'topic' => 'string',
            'competency' => 'string',
            'answer_key' => 'string',
            'max_words' => 'integer',
            'is_active' => 'boolean',
        ]);
        $question = CbtQuestion::create($data);
        return response()->json($question, 201);
    }

    // Update question
    public function update(Request $request, $id)
    {
        $question = CbtQuestion::findOrFail($id);
        $data = $request->validate([
            'question_type' => 'string',
            'question_text' => 'string',
            'question_image' => 'nullable|string',
            'explanation' => 'nullable|string',
            'points' => 'integer',
            'difficulty' => 'string',
            'topic' => 'string',
            'competency' => 'string',
            'answer_key' => 'string',
            'max_words' => 'integer',
            'is_active' => 'boolean',
        ]);
        $question->update($data);
        return response()->json($question);
    }

    // Delete question
    public function destroy($id)
    {
        $question = CbtQuestion::findOrFail($id);
        $question->delete();
        return response()->json(['message' => 'Question deleted']);
    }
}
