<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceContract;
use App\Models\AcademicYear;

class PerformanceContractController extends Controller
{
    /**
     * Tampilkan daftar perjanjian kinerja (Menyesuaikan Role)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $currentYear = AcademicYear::where('is_active', 1)->first();

        $query = PerformanceContract::with(['employee', 'academicYear', 'position'])
            ->orderBy('created_at', 'desc');

        if ($user->isSuperAdmin()) {
            // YAYASAN: Melihat kontrak yang SUDAH di-ACC Kepsek atau di-ACC Yayasan
            $query->whereIn('status', [
                PerformanceContract::STATUS_APPROVED_BY_KEPSEK,
                PerformanceContract::STATUS_APPROVED_BY_YAYASAN,
                PerformanceContract::STATUS_REJECTED // Yayasan juga bisa nolak
            ]);
            $viewTitle = 'Finalisasi Perjanjian Kinerja (Yayasan)';
        } else {
            // KEPSEK / ADMIN SEKOLAH: Melihat kontrak dari sekolahnya saja
            $query->where('school_id', $user->school_id);
            $viewTitle = 'Validasi Perjanjian Kinerja Guru';
        }

        $contracts = $query->paginate(20);

        return view('admin.performance_contracts.index', compact('contracts', 'viewTitle', 'currentYear'));
    }

    /**
     * Lihat detail kontrak untuk di-ACC/Tolak
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $contract = PerformanceContract::with(['employee', 'academicYear', 'position', 'school'])->findOrFail($id);

        // Security check
        if (!$user->isSuperAdmin() && $contract->school_id !== $user->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return view('admin.performance_contracts.show', compact('contract'));
    }

    /**
     * Proses ACC atau Penolakan
     */
    public function process(Request $request, $id)
    {
        $user = auth()->user();
        $contract = PerformanceContract::findOrFail($id);

        if (!$user->isSuperAdmin() && $contract->school_id !== $user->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'required_if:action,reject',
        ]);

        if ($validated['action'] === 'approve') {
            if ($user->isSuperAdmin()) {
                // Yayasan -> ACC Final
                $contract->status = PerformanceContract::STATUS_APPROVED_BY_YAYASAN;
            } else {
                // Kepsek -> ACC Tahap 1
                $contract->status = PerformanceContract::STATUS_APPROVED_BY_KEPSEK;
            }
            $contract->notes = null;
            $msg = 'Perjanjian Kinerja berhasil disetujui.';
        } else {
            // Ditolak
            $contract->status = PerformanceContract::STATUS_REJECTED;
            $contract->notes = $validated['notes'];
            $msg = 'Perjanjian Kinerja dikembalikan/ditolak.';
        }

        $contract->save();

        return redirect()->route('admin.performance_contracts.index')->with('success', $msg);
    }
}
