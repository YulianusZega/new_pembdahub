<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeWeight;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeWeightController extends Controller
{
    /**
     * Show grade weight configuration for all schools
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $schools = School::where('is_active', true)->schoolsOnly()->orderBy('name')->get();
        } else {
            $schools = School::where('id', $user->school_id)->get();
        }

        // Load weights for each school
        $schoolWeights = [];
        foreach ($schools as $school) {
            $schoolWeights[] = [
                'school' => $school,
                'weights' => GradeWeight::getForSchool($school->id),
            ];
        }

        return view('admin.grade-weights.index', compact('schoolWeights'));
    }

    /**
     * Update grade weights for a school
     */
    public function update(Request $request, School $school)
    {
        $user = auth()->user();

        // Authorization
        if ($user->role !== 'superadmin' && $user->school_id !== $school->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tugas_weight' => 'required|numeric|min:0|max:100',
            'pts_weight' => 'required|numeric|min:0|max:100',
            'pas_weight' => 'required|numeric|min:0|max:100',
            'sikap_weight' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        // Validate total = 100%
        $total = $validated['tugas_weight'] + $validated['pts_weight'] + $validated['pas_weight'] + $validated['sikap_weight'];
        if (abs($total - 100) > 0.01) {
            return back()->withErrors(['weights' => "Total bobot harus 100%. Saat ini: {$total}%"])->withInput();
        }

        $weight = GradeWeight::getForSchool($school->id);
        $weight->update([
            'tugas_weight' => $validated['tugas_weight'],
            'pts_weight' => $validated['pts_weight'],
            'pas_weight' => $validated['pas_weight'],
            'sikap_weight' => $validated['sikap_weight'],
            'description' => $validated['description'] ?? "Tugas {$validated['tugas_weight']}% + PTS {$validated['pts_weight']}% + PAS {$validated['pas_weight']}% + Sikap {$validated['sikap_weight']}%",
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', "Bobot nilai untuk {$school->name} berhasil diperbarui.");
    }

    /**
     * Reset weights to default for a school
     */
    public function reset(School $school)
    {
        $user = auth()->user();

        if ($user->role !== 'superadmin' && $user->school_id !== $school->id) {
            abort(403);
        }

        $weight = GradeWeight::getForSchool($school->id);
        $weight->update([
            'tugas_weight' => GradeWeight::DEFAULT_TUGAS,
            'pts_weight' => GradeWeight::DEFAULT_PTS,
            'pas_weight' => GradeWeight::DEFAULT_PAS,
            'sikap_weight' => GradeWeight::DEFAULT_SIKAP,
            'description' => 'Default: Tugas 20% + PTS 30% + PAS 40% + Sikap 10%',
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', "Bobot nilai untuk {$school->name} direset ke default.");
    }
}
