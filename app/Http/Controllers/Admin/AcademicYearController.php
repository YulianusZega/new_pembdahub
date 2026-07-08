<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
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
        $query = AcademicYear::query()->orderByDesc('is_active')->orderBy('start_date');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('year', 'like', "%{$q}%");
        }

        $academicYears = $query->paginate(15)->withQueryString();

        return view('admin.academic_years.index', compact('academicYears'));
    }

    public function toggleActive(Request $request, $id)
    {
        $this->authorizeAccess();
        $academicYear = AcademicYear::findOrFail($id);

        if ($request->has('set_active')) {
            $setActive = (bool)$request->input('set_active');

            if (!$setActive) {
                // Ensure at least one academic year remains active
                $activeCount = AcademicYear::where('is_active', true)->count();
                if ($academicYear->is_active && $activeCount <= 1) {
                    if ($request->wantsJson() || $request->ajax() || $request->accepts('application/json')) {
                        return response()->json([
                            'error' => 'Tidak dapat menonaktifkan tahun ajaran aktif. Silakan aktifkan tahun ajaran lainnya terlebih dahulu.'
                        ], 422);
                    }
                    return redirect()->route('admin.academic-years.index')
                        ->with('error', 'Tidak dapat menonaktifkan tahun ajaran aktif. Silakan aktifkan tahun ajaran lainnya terlebih dahulu.');
                }
            }

            if ($setActive) {
                // disable other academic years globally
                AcademicYear::query()->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }

            // Update explicitly in database to bypass any in-memory Eloquent dirty checks
            AcademicYear::query()->where('id', $academicYear->id)->update(['is_active' => $setActive]);
            $academicYear->is_active = $setActive;
        }

        if ($request->wantsJson() || $request->ajax() || $request->accepts('application/json')) {
            return response()->json(['is_active' => $academicYear->is_active]);
        }

        return redirect()->route('admin.academic-years.index')->with('success', 'Status aktif diperbarui.');
    }

    public function create()
    {
        $this->authorizeAccess();
        return view('admin.academic_years.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:academic_years'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'semester_start' => ['nullable', 'date'],
            'semester_end' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        AcademicYear::create($data);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function show(AcademicYear $academicYear)
    {
        $this->authorizeAccess();
        return view('admin.academic_years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        $this->authorizeAccess();
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $this->authorizeAccess();
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', Rule::unique('academic_years')->ignore($academicYear->id)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'semester_start' => ['nullable', 'date'],
            'semester_end' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        $academicYear->update($data);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, AcademicYear $academicYear)
    {
        $this->authorizeAccess();

        // Hitung data terkait sebelum menghapus
        $relatedData = [
            'Semester' => $academicYear->semesters()->count(),
            'Kelas (Classroom)' => \DB::table('classrooms')->where('academic_year_id', $academicYear->id)->count(),
            'Penempatan Siswa' => \DB::table('student_classes')->where('academic_year_id', $academicYear->id)->count(),
            'Tagihan Siswa' => \DB::table('student_bills')->where('academic_year_id', $academicYear->id)->count(),
            'Jadwal' => \DB::table('schedules')->where('academic_year_id', $academicYear->id)->count(),
            'Penugasan Guru' => \DB::table('teaching_assignments')->where('academic_year_id', $academicYear->id)->count(),
            'Rapor' => \DB::table('report_cards')->where('academic_year_id', $academicYear->id)->count(),
            'Jabatan Pegawai' => \DB::table('employee_positions')->where('academic_year_id', $academicYear->id)->count(),
        ];

        $totalRelated = array_sum($relatedData);

        // Jika ada data terkait, TOLAK penghapusan
        if ($totalRelated > 0) {
            $details = [];
            foreach ($relatedData as $label => $count) {
                if ($count > 0) {
                    $details[] = "$label: $count data";
                }
            }
            $message = 'TIDAK DAPAT MENGHAPUS tahun ajaran "' . $academicYear->year . '" karena masih memiliki data terkait: ' . implode(', ', $details) . '. Hapus data terkait terlebih dahulu atau hubungi administrator.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $message], 422);
            }
            return redirect()->route('admin.academic-years.index')->with('error', $message);
        }

        try {
            $academicYear->delete();
            return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Tidak dapat menghapus tahun ajaran karena masih digunakan dalam data lain.');
        }
    }
}
