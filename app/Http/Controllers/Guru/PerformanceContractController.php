<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceContract;
use App\Models\AcademicYear;
use App\Models\Position;

class PerformanceContractController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->teacher) {
            abort(403, 'Akses khusus Guru');
        }

        $contracts = PerformanceContract::where('employee_id', $user->teacher->employee_id)
            ->with(['academicYear', 'position'])
            ->orderBy('created_at', 'desc')
            ->get();

        $currentYear = AcademicYear::where('is_active', 1)->first();

        return view('guru.performance_contracts.index', compact('contracts', 'currentYear'));
    }

    public function create()
    {
        $currentYear = AcademicYear::where('is_active', 1)->first();
        if (!$currentYear) {
            return redirect()->route('guru.performance_contracts.index')->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        // Ambil daftar jabatan struktural yang bisa dipilih untuk Form 4
        $user = auth()->user();
        $positions = Position::where('school_id', $user->school_id)
            ->whereNotIn('position_code', ['KASEK', 'WAKEL'])
            ->orderBy('position_name')
            ->get();

        return view('guru.performance_contracts.create', compact('currentYear', 'positions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'contract_type' => 'required|in:pkg_kejuruan,pkg_umum,jabatan_tambahan',
            'position_id' => 'required_if:contract_type,jabatan_tambahan|nullable|exists:positions,id',
            'target_data' => 'required|array',
        ]);

        $currentYear = AcademicYear::where('is_active', 1)->first();

        // Cek apakah sudah ada kontrak serupa yang sedang diajukan
        $exists = PerformanceContract::where('employee_id', $user->teacher->employee_id)
            ->where('academic_year_id', $currentYear->id)
            ->where('contract_type', $validated['contract_type'])
            ->when($validated['contract_type'] === 'jabatan_tambahan', function($q) use ($validated) {
                return $q->where('position_id', $validated['position_id']);
            })
            ->whereIn('status', [PerformanceContract::STATUS_DRAFT, PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK, PerformanceContract::STATUS_APPROVED_BY_KEPSEK, PerformanceContract::STATUS_APPROVED_BY_YAYASAN])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan kontrak kinerja untuk tipe ini di tahun ajaran ini.');
        }

        $contract = PerformanceContract::create([
            'employee_id' => $user->teacher->employee_id,
            'academic_year_id' => $currentYear->id,
            'school_id' => $user->school_id,
            'contract_type' => $validated['contract_type'],
            'position_id' => $validated['contract_type'] === 'jabatan_tambahan' ? $validated['position_id'] : null,
            'target_data' => $validated['target_data'],
            'status' => PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK,
        ]);

        return redirect()->route('guru.performance_contracts.index')->with('success', 'Kontrak Kinerja berhasil diajukan ke Kepala Sekolah.');
    }

    public function print($id)
    {
        $user = auth()->user();
        $contract = PerformanceContract::where('employee_id', $user->teacher->employee_id)
            ->where('id', $id)
            ->with(['academicYear', 'position', 'employee'])
            ->firstOrFail();

        if ($contract->status !== PerformanceContract::STATUS_APPROVED_BY_YAYASAN) {
            abort(403, 'Kontrak belum disetujui Yayasan, tidak dapat dicetak.');
        }

        return view('guru.performance_contracts.print', compact('contract'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $contract = PerformanceContract::where('employee_id', $user->teacher->employee_id)
            ->where('id', $id)
            ->with(['academicYear', 'position'])
            ->firstOrFail();

        return view('guru.performance_contracts.show', compact('contract'));
    }
}
