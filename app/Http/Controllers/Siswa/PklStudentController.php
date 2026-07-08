<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PklPlacement;
use App\Models\PklLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PklStudentController extends Controller
{
    private function getStudent(): Student
    {
        return Student::where('user_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        $student = $this->getStudent();
        
        $placement = PklPlacement::where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['teacher', 'logs' => function($q) {
                $q->orderByDesc('log_date');
            }, 'grade'])
            ->first();

        return view('siswa.pkl.index', compact('student', 'placement'));
    }

    public function storeLog(Request $request)
    {
        $student = $this->getStudent();
        
        $placement = PklPlacement::where('student_id', $student->id)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'log_date' => 'required|date|before_or_equal:today',
            'activity' => 'required|string|min:10',
            'photo' => 'nullable|image|max:5120', // max 5MB
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Check if log for this date already exists
        $exists = PklLog::where('pkl_placement_id', $placement->id)
            ->where('log_date', $validated['log_date'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah mengisi logbook untuk tanggal ini.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pkl_proofs', 'public');
        }

        PklLog::create([
            'pkl_placement_id' => $placement->id,
            'log_date' => $validated['log_date'],
            'activity' => $validated['activity'],
            'photo' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => 'submitted',
        ]);

        return redirect()->route('siswa.pkl.index')->with('success', 'Logbook harian berhasil dikirim.');
    }
}
