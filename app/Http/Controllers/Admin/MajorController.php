<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\School;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    private function authorizeAccess()
    {
        if (! optional(auth()->user())->hasAnyRole(['superadmin', 'admin_sekolah'])) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $user = auth()->user();
        
        // Build query for majors (SMA/SMP only)
        $query = Major::with('school')
            ->whereHas('school', function($q) {
                // Only show majors from SMA/SMP schools, exclude SMK
                $q->whereIn('type', ['SMA', 'SMP']);
            })
            ->orderBy('major_code');
        
        // Auto-filter by school_id for admin_sekolah (only if their school is SMA/SMP)
        if (!$user->isSuperAdmin() && $user->school_id) {
            $userSchool = School::find($user->school_id);
            // Only apply school_id filter if user's school is SMA/SMP
            if ($userSchool && in_array($userSchool->type, ['SMA', 'SMP'])) {
                $query->where('school_id', $user->school_id);
            }
        }
        
        // Manual filter for superadmin
        if ($request->filled('school_id') && $user->isSuperAdmin()) {
            $query->where('school_id', $request->input('school_id'));
        }
        
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($query) use ($q) {
                $query->where('major_code', 'like', "%{$q}%")
                      ->orWhere('major_name', 'like', "%{$q}%");
            });
        }

        $majors = $query->paginate(15)->withQueryString();
        $schools = School::where('is_active', true)->orderBy('name')->get();
        
        // Get Program Keahlian for SMK tab
        $programKeahlians = ProgramKeahlian::with(['school', 'konsentrasiKeahlians'])
            ->whereHas('school', function($q) {
                $q->where('type', 'SMK');
            })
            ->orderBy('nama')
            ->get();

        return view('admin.majors.index', compact('majors', 'schools', 'programKeahlians'));
    }

    public function create()
    {
        $this->authorizeAccess();
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('admin.majors.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'major_code' => ['required', 'string', 'max:20', Rule::unique('majors')->where(function ($q) use ($request) {
                return $q->where('school_id', $request->input('school_id'));
            })],
            'major_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        Major::create($data);

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Major $major)
    {
        $this->authorizeAccess();
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('admin.majors.edit', compact('major', 'schools'));
    }

    public function update(Request $request, Major $major)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'major_code' => ['required', 'string', 'max:20', Rule::unique('majors')->where(function ($q) use ($request, $major) {
                return $q->where('school_id', $request->input('school_id'))->where('id', '!=', $major->id);
            })],
            'major_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        $major->update($data);

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        $this->authorizeAccess();
        $major->delete();
        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}

