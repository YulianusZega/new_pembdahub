<?php

namespace App\Http\Controllers;

use App\Models\CbtQuestionOption;
use Illuminate\Http\Request;

class CbtQuestionOptionController extends Controller
{
    // List all options for a question
    public function index($questionId)
    {
        $options = CbtQuestionOption::where('question_id', $questionId)->get();
        return response()->json($options);
    }

    // Show a single option
    public function show($id)
    {
        $option = CbtQuestionOption::findOrFail($id);
        return response()->json($option);
    }

    // Create a new option
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required|exists:cbt_questions,id',
            'option_text' => 'required|string',
            'option_image' => 'nullable|string',
            'is_correct' => 'boolean',
            'order' => 'integer',
        ]);
        $option = CbtQuestionOption::create($data);
        return response()->json($option, 201);
    }

    // Update option
    public function update(Request $request, $id)
    {
        $option = CbtQuestionOption::findOrFail($id);
        $data = $request->validate([
            'option_text' => 'string',
            'option_image' => 'nullable|string',
            'is_correct' => 'boolean',
            'order' => 'integer',
        ]);
        $option->update($data);
        return response()->json($option);
    }

    // Delete option
    public function destroy($id)
    {
        $option = CbtQuestionOption::findOrFail($id);
        $option->delete();
        return response()->json(['message' => 'Option deleted']);
    }
}
