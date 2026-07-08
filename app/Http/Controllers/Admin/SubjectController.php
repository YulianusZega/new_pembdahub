<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Subject::with('school', 'major');

        // Auto-filter by school_id for admin_sekolah
        if ($user && !$user->isSuperAdmin()) {
            $query->where('school_id', $user->school_id);
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($query) use ($q) {
                $query->where('subject_name', 'like', "%{$q}%")
                      ->orWhere('subject_code', 'like', "%{$q}%");
            });
        }

        // Manual filter for superadmin
        if ($request->filled('school_id') && $user && $user->isSuperAdmin()) {
            $query->where('school_id', $request->input('school_id'));
        }

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->input('major_id'));
        }

        $subjects = $query->orderBy('subject_name')->paginate(20)->withQueryString();

        $schools = $user->isSuperAdmin() 
            ? School::orderBy('name')->get() 
            : School::where('id', $user->school_id)->get();
            
        $majors = \App\Models\Major::when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('major_name')
            ->get();

        return view('admin.subjects.index', compact('subjects', 'schools', 'majors'));
    }

    public function create()
    {
        $user = auth()->user();
        $schools = $user->isSuperAdmin() 
            ? School::orderBy('name')->get() 
            : School::where('id', $user->school_id)->get();
            
        $majors = \App\Models\Major::when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('major_name')
            ->get();
            
        return view('admin.subjects.create', compact('schools', 'majors'));
    }

    /**
     * Import form
     */
    public function importForm()
    {
        return view('admin.subjects.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $count = 0;

        \DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_combine($header, $row);
                if (empty($row['subject_name'])) {
                    continue;
                }

                $data = [
                    'school_id' => $row['school_id'] ?? auth()->user()->school_id ?? null,
                    'major_id' => $row['major_id'] ?? null,
                    'subject_code' => $row['subject_code'] ?? null,
                    'subject_name' => $row['subject_name'] ?? null,
                    'description' => $row['description'] ?? null,
                    'kkm' => isset($row['kkm']) && $row['kkm'] !== '' ? (int)$row['kkm'] : null,
                    'is_active' => isset($row['is_active']) && in_array(strtolower($row['is_active']), ['1', 'true', 'ya', 'yes']) ? true : false,
                ];

                Subject::updateOrCreate([
                    'subject_code' => $data['subject_code'],
                    'subject_name' => $data['subject_name'],
                ], $data);

                $count++;
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Gagal mengimpor mata pelajaran: ' . $e->getMessage());
            return back()->withErrors(['csv' => 'Terjadi kesalahan saat mengimpor. Silakan coba lagi.']);
        }

        return redirect()->route('admin.subjects.index')->with('success', "Import selesai. $count mata pelajaran diproses.");
    }

    public function downloadSampleCsv()
    {
        $publicDir = public_path('csv');
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $path = $publicDir . DIRECTORY_SEPARATOR . 'sample_subjects.csv';

        if (!file_exists($path)) {
            $content = "school_id,major_id,subject_code,subject_name,description,kkm,is_active\n";
            $content .= "1,1,MATH,Matematika,Bahasan dasar matematika,75,1\n";
            file_put_contents($path, $content);
        }

        return response()->download($path, 'sample_subjects.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'major_id' => 'nullable|exists:majors,id',
            'subject_code' => 'nullable|string|max:20',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'kkm' => 'nullable|integer|min:0|max:100',
        ]);

        // Force school_id for non-SA
        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }
        
        Subject::create($data);
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran ditambahkan.');
    }

    public function show(Subject $subject)
    {
        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $user = auth()->user();
        $schools = $user->isSuperAdmin() 
            ? School::orderBy('name')->get() 
            : School::where('id', $user->school_id)->get();
            
        $majors = \App\Models\Major::when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('major_name')
            ->get();
            
        return view('admin.subjects.edit', compact('subject', 'schools', 'majors'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'subject_code' => 'nullable|string|max:20',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'kkm' => 'nullable|integer|min:0|max:100',
        ]);
        $subject->update($data);
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Tidak dapat menghapus mata pelajaran karena masih digunakan (jadwal, nilai, ujian, dll).');
        }
    }
}
