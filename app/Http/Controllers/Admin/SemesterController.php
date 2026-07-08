<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
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

        $query = Semester::with('academicYear')->orderByDesc('is_active')->orderBy('semester_number');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('semester_name', 'like', "%{$q}%");
        }

        $semesters = $query->paginate(15)->withQueryString();
        $academicYears = AcademicYear::orderBy('year')->get();

        return view('admin.semesters.index', compact('semesters', 'academicYears'));
    }

    public function create()
    {
        $this->authorizeAccess();
        $academicYears = AcademicYear::orderBy('year')->get();
        return view('admin.semesters.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_number' => ['required', 'integer', 'in:1,2'],
            'semester_name' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        if ($data['is_active']) {
            Semester::query()->update(['is_active' => false]);
        }

        Semester::create($data);

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function show(Semester $semester)
    {
        $this->authorizeAccess();
        return view('admin.semesters.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        $this->authorizeAccess();
        $academicYears = AcademicYear::orderBy('year')->get();
        return view('admin.semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(Request $request, Semester $semester)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_number' => ['required', 'integer', 'in:1,2'],
            'semester_name' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        if ($data['is_active']) {
            Semester::query()->update(['is_active' => false]);
        }

        $semester->update($data);

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $this->authorizeAccess();
        try {
            $semester->delete();
            return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.semesters.index')
                ->with('error', 'Tidak dapat menghapus semester karena masih digunakan dalam data nilai, tagihan, atau jadwal.');
        }
    }
}
