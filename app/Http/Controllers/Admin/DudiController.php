<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dudi;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DudiController extends Controller
{
    private function isSuperAdmin(): bool
    {
        return Auth::user()->isSuperAdmin();
    }

    private function getSchoolId()
    {
        return Auth::user()->school_id;
    }

    public function index(Request $request)
    {
        $isSA = $this->isSuperAdmin();
        $schoolId = $this->getSchoolId();

        $query = Dudi::with('school');

        if (!$isSA) {
            // Admin sekolah hanya bisa melihat DUDI yang global (null) atau miliknya sendiri
            $query->where(function($q) use ($schoolId) {
                $q->whereNull('school_id')
                  ->orWhere('school_id', $schoolId);
            });
        } elseif ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mentor_name', 'like', '%' . $request->search . '%');
        }

        $dudis = $query->latest()->paginate(15)->withQueryString();
        $schools = School::where('type', 'SMK')->get();

        return view('admin.pkl_alumni.dudis.index', compact('dudis', 'schools', 'isSA'));
    }

    public function create()
    {
        $isSA = $this->isSuperAdmin();
        $schools = School::where('type', 'SMK')->get();
        return view('admin.pkl_alumni.dudis.create', compact('isSA', 'schools'));
    }

    public function store(Request $request)
    {
        $isSA = $this->isSuperAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'field_of_work' => 'nullable|string|max:255',
            'mentor_name' => 'nullable|string|max:255',
            'mentor_phone' => 'nullable|string|max:50',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if (!$isSA) {
            $validated['school_id'] = $this->getSchoolId();
        }

        Dudi::create($validated);

        return redirect()->route('admin.pkl-alumni.dudis.index')->with('success', 'Data Mitra DUDI berhasil ditambahkan.');
    }

    public function edit(Dudi $dudi)
    {
        $isSA = $this->isSuperAdmin();
        
        if (!$isSA && $dudi->school_id !== null && $dudi->school_id !== $this->getSchoolId()) {
            abort(403);
        }
        
        $schools = School::where('type', 'SMK')->get();
        return view('admin.pkl_alumni.dudis.edit', compact('dudi', 'isSA', 'schools'));
    }

    public function update(Request $request, Dudi $dudi)
    {
        $isSA = $this->isSuperAdmin();
        
        if (!$isSA && $dudi->school_id !== null && $dudi->school_id !== $this->getSchoolId()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'field_of_work' => 'nullable|string|max:255',
            'mentor_name' => 'nullable|string|max:255',
            'mentor_phone' => 'nullable|string|max:50',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if (!$isSA && $dudi->school_id !== null) {
            $validated['school_id'] = $this->getSchoolId();
        }

        $dudi->update($validated);

        return redirect()->route('admin.pkl-alumni.dudis.index')->with('success', 'Data Mitra DUDI berhasil diperbarui.');
    }

    public function destroy(Dudi $dudi)
    {
        $isSA = $this->isSuperAdmin();
        
        if (!$isSA && $dudi->school_id !== null && $dudi->school_id !== $this->getSchoolId()) {
            abort(403);
        }

        $dudi->delete();
        return redirect()->route('admin.pkl-alumni.dudis.index')->with('success', 'Data Mitra DUDI berhasil dihapus.');
    }
}
