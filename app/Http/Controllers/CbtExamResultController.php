<?php

namespace App\Http\Controllers;

use App\Models\CbtExamResult;
use Illuminate\Http\Request;

class CbtExamResultController extends Controller
{
    // List all results for a participant
    public function index($participantId)
    {
        $results = CbtExamResult::where('participant_id', $participantId)->get();
        return response()->json($results);
    }

    // Show a single result
    public function show($id)
    {
        $result = CbtExamResult::findOrFail($id);
        return response()->json($result);
    }

    // Create a new result
    public function store(Request $request)
    {
        $data = $request->validate([
            'participant_id' => 'required|exists:cbt_exam_participants,id',
            'total_score' => 'integer',
            'grade' => 'string',
            'status' => 'string',
            'feedback' => 'nullable|string',
            'finalized_at' => 'date',
        ]);
        $result = CbtExamResult::create($data);
        return response()->json($result, 201);
    }

    // Update result
    public function update(Request $request, $id)
    {
        $result = CbtExamResult::findOrFail($id);
        $data = $request->validate([
            'total_score' => 'integer',
            'grade' => 'string',
            'status' => 'string',
            'feedback' => 'nullable|string',
            'finalized_at' => 'date',
        ]);
        $result->update($data);
        return response()->json($result);
    }

    // Delete result
    public function destroy($id)
    {
        $result = CbtExamResult::findOrFail($id);
        $result->delete();
        return response()->json(['message' => 'Result deleted']);
    }
}
