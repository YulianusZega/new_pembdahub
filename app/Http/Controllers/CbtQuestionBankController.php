<?php

namespace App\Http\Controllers;

use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use Illuminate\Http\Request;

class CbtQuestionBankController extends Controller
{
    // List all question banks
    public function index()
    {
        $banks = CbtQuestionBank::with(['subject', 'teacher'])->get();
        return response()->json($banks);
    }

    // Show a single question bank
    public function show($id)
    {
        $bank = CbtQuestionBank::with(['subject', 'teacher', 'questions'])->findOrFail($id);
        return response()->json($bank);
    }

    // Create a new question bank
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $bank = CbtQuestionBank::create($data);
        return response()->json($bank, 201);
    }

    // Update question bank
    public function update(Request $request, $id)
    {
        $bank = CbtQuestionBank::findOrFail($id);
        $data = $request->validate([
            'title' => 'string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $bank->update($data);
        return response()->json($bank);
    }

    // Delete question bank
    public function destroy($id)
    {
        $bank = CbtQuestionBank::findOrFail($id);
        $bank->delete();
        return response()->json(['message' => 'Question bank deleted']);
    }
}
