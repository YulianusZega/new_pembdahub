<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        // Select spesifik untuk list view + load relasi principal (kecualikan Yayasan)
        $schools = School::schoolsOnly()
            ->select('id', 'name', 'type', 'npsn', 'phone', 'email', 'principal_id', 'is_active', 'address')
            ->withCount(['students', 'classrooms'])
            ->with(['principal:id,employee_id,teacher_code,full_name', 'principal.employee:id,photo'])
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        // Tampilkan semua guru yang sudah punya jabatan pimpinan (Level 5/10) 
        // ATAU yang termasuk dalam kategori pimpinan potensial
        $teachers = \App\Models\Teacher::where('is_active', 1)
            ->where(function($query) {
                // Berikan prioritas pada yang sudah menjabat pimpinan
                $query->whereHas('employee.positions', function($sq) {
                    $sq->whereBetween('positions.position_level', [1, 15])
                       ->whereNull('employee_positions.end_date');
                })
                // Atau tampilkan semua guru agar bisa ditunjuk (khusus filter global tidak ada school_id)
                ->orWhere('id', '>', 0); 
            })
            ->orderBy('full_name')
            ->get(['id', 'teacher_code', 'full_name', 'school_id']);
        
        return view('admin.schools.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:20',
            'npsn' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'principal_name' => 'nullable|string|max:100',
            'principal_id' => 'nullable|exists:teachers,id',
            'school_year_start' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        $school = School::create($data);

        // Auto-assign jabatan Kepala Sekolah jika principal_id dipilih
        if ($request->filled('principal_id')) {
            $this->assignPrincipalPosition($request->principal_id, $school->id);
        }

        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function show(School $school)
    {
        return view('admin.schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        // Dropdown Kepala Sekolah / Pimpinan Unit:
        // 1. Ambil semua guru di sekolah ini (agar bisa promosi)
        // 2. Ambil semua orang yang sudah punya jabatan pimpinan (Level 5/10) di sistem (agar bisa mutasi)
        $teachers = \App\Models\Teacher::where('is_active', 1)
            ->where(function($query) use ($school) {
                $query->where('school_id', $school->id)
                      ->orWhereHas('employee.positions', function($sq) {
                          $sq->whereBetween('positions.position_level', [1, 15])
                             ->whereNull('employee_positions.end_date');
                      });
            })
            ->orderBy('full_name')
            ->get(['id', 'teacher_code', 'full_name', 'school_id']);
        
        // Cek apakah sekolah ini sudah memiliki Kepala Sekolah aktif untuk tahun ajaran aktif
        $currentYear = \App\Models\AcademicYear::where('is_active', 1)->first();
        $hasPrincipal = false;
        
        if ($school->principal_id) {
            $hasPrincipal = \DB::table('employee_positions')
                ->join('positions', 'employee_positions.position_id', '=', 'positions.id')
                ->where('positions.school_id', $school->id)
                ->where('positions.position_code', 'LIKE', 'KASEK%')
                ->where('employee_positions.academic_year_id', $currentYear ? $currentYear->id : null)
                ->whereNull('employee_positions.end_date')
                ->exists();
        }
        
        $showAllTeachers = !$hasPrincipal;
        
        return view('admin.schools.edit', compact('school', 'teachers', 'showAllTeachers'));
    }

    public function update(Request $request, School $school)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:20',
            'npsn' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'principal_name' => 'nullable|string|max:100',
            'principal_id' => 'nullable|exists:teachers,id',
            'school_year_start' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : $school->is_active;

        $school->update($data);

        // Auto-assign jabatan Kepala Sekolah jika principal_id dipilih dan belum punya jabatan
        if ($request->filled('principal_id')) {
            $this->assignPrincipalPosition($request->principal_id, $school->id);
        }

        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        try {
            $school->delete();
            return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.schools.index')
                ->with('error', 'Tidak dapat menghapus sekolah karena masih memiliki data terkait (siswa, guru, kelas, dll).');
        }
    }

    /**
     * Auto-assign jabatan Kepala Sekolah jika belum ada
     */
    private function assignPrincipalPosition($teacherId, $schoolId)
    {
        $teacher = \App\Models\Teacher::find($teacherId);
        
        if (!$teacher || !$teacher->employee_id) {
            return; // Skip jika teacher tidak punya employee record
        }

        // Cari jabatan Kepala Sekolah untuk unit sekolah ini (e.g. KASEK-SMP, KASEK-SMA, KASEK-SMK)
        $position = \App\Models\Position::where('school_id', $schoolId)
            ->where('position_code', 'LIKE', 'KASEK%')
            ->first();

        if (!$position) {
            // Fallback ke posisi structural pimpinan lainnya untuk unit ini
            $position = \App\Models\Position::where('school_id', $schoolId)
                ->where('position_category', 'structural')
                ->orderBy('position_level', 'asc')
                ->first();
        }

        if (!$position) {
            return; // Tidak bisa lanjut jika jabatan tidak ditemukan
        }

        $academicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
        $academicYearId = $academicYear ? $academicYear->id : null;

        // 1. Nonaktifkan penugasan Kepala Sekolah lama untuk jabatan ini di unit ini pada tahun ajaran aktif
        \DB::table('employee_positions')
            ->where('position_id', $position->id)
            ->where('employee_id', '!=', $teacher->employee_id)
            ->where('academic_year_id', $academicYearId)
            ->whereNull('end_date')
            ->update([
                'end_date' => now()->format('Y-m-d'),
                'updated_at' => now(),
            ]);

        // 2. Cek apakah Kepala Sekolah baru sudah terdaftar aktif untuk jabatan ini pada tahun ajaran aktif
        $hasActiveKepsek = \DB::table('employee_positions')
            ->where('employee_id', $teacher->employee_id)
            ->where('position_id', $position->id)
            ->where('academic_year_id', $academicYearId)
            ->whereNull('end_date')
            ->exists();

        // 3. Jika belum punya, create penugasan baru
        if (!$hasActiveKepsek) {
            \DB::table('employee_positions')->insert([
                'employee_id' => $teacher->employee_id,
                'position_id' => $position->id,
                'academic_year_id' => $academicYearId,
                'semester' => 'full_year',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => null,
                'is_primary' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Return majors for a school (JSON) - used for dynamic selects in forms
     */
    public function majors(School $school)
    {
        $majors = $school->majors()->where('is_active', true)->orderBy('major_name')->get(['id', 'major_name']);
        return response()->json([
            'type' => $school->type,
            'majors' => $majors,
        ]);
    }

    /**
     * Return program keahlian and konsentrasi keahlian for a school (JSON)
     */
    public function keahlian(School $school)
    {
        $programs = $school->programKeahlians()->where('is_active', true)->orderBy('nama')->get(['id', 'nama']);
        $konsentrasi = [];
        if ($programs->count()) {
            foreach ($programs as $prog) {
                $konsentrasi[$prog->id] = $prog->konsentrasiKeahlians()->where('is_active', true)->orderBy('nama')->get(['id', 'nama']);
            }
        }
        $majors = $school->majors()->where('is_active', true)->orderBy('major_name')->get(['id', 'major_name']);
        $gradeLevels = $school->getGradeLevels();

        return response()->json([
            'type' => $school->type,
            'program_keahlians' => $programs,
            'konsentrasi_keahlians' => $konsentrasi,
            'majors' => $majors,
            'grade_levels' => $gradeLevels,
        ]);
    }
}
