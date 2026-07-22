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
        $isYayasanView = $user->isSuperAdmin() || $user->isYayasan() || $request->routeIs('yayasan.*');

        if ($isYayasanView) {
            $viewTitle = 'Finalisasi Perjanjian Kinerja (Yayasan)';
        } else {
            $viewTitle = 'Validasi Perjanjian Kinerja Guru';
        }

        // Base query untuk menghitung jumlah tab
        $baseCountQuery = PerformanceContract::query();
        if (!$isYayasanView) {
            $baseCountQuery->where('school_id', $user->school_id);
        } else {
            $baseCountQuery->whereIn('status', [
                PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK,
                PerformanceContract::STATUS_APPROVED_BY_KEPSEK,
                PerformanceContract::STATUS_APPROVED_BY_YAYASAN,
                PerformanceContract::STATUS_REJECTED
            ]);
        }

        $countsRaw = (clone $baseCountQuery)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusCounts = [
            'approved_by_yayasan' => $countsRaw[PerformanceContract::STATUS_APPROVED_BY_YAYASAN] ?? 0,
            'approved_by_kepsek' => $countsRaw[PerformanceContract::STATUS_APPROVED_BY_KEPSEK] ?? 0,
            'submitted_to_kepsek' => $countsRaw[PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK] ?? 0,
            'rejected' => $countsRaw[PerformanceContract::STATUS_REJECTED] ?? 0,
        ];
        $statusCounts['all'] = array_sum($statusCounts);

        $tab = $request->get('tab', 'all');

        $query = PerformanceContract::with(['employee', 'academicYear', 'position', 'school'])
            ->orderBy('created_at', 'desc');

        if (!$isYayasanView) {
            $query->where('school_id', $user->school_id);
        }

        if ($tab === 'approved_by_yayasan') {
            $query->where('status', PerformanceContract::STATUS_APPROVED_BY_YAYASAN);
        } elseif ($tab === 'approved_by_kepsek') {
            $query->where('status', PerformanceContract::STATUS_APPROVED_BY_KEPSEK);
        } elseif ($tab === 'submitted_to_kepsek') {
            $query->where('status', PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK);
        } elseif ($tab === 'rejected') {
            $query->where('status', PerformanceContract::STATUS_REJECTED);
        } else {
            // Default: tampilkan semua status yang diajukan
            $query->whereIn('status', [
                PerformanceContract::STATUS_SUBMITTED_TO_KEPSEK,
                PerformanceContract::STATUS_APPROVED_BY_KEPSEK,
                PerformanceContract::STATUS_APPROVED_BY_YAYASAN,
                PerformanceContract::STATUS_REJECTED
            ]);
        }

        $contracts = $query->paginate(20)->withQueryString();

        return view('admin.performance_contracts.index', compact(
            'contracts', 'viewTitle', 'currentYear', 'tab', 'statusCounts', 'isYayasanView'
        ));
    }

    /**
     * Lihat detail kontrak untuk di-ACC/Tolak
     */
    public function show($id)
    {
        $user = auth()->user();
        $isYayasanView = $user->isSuperAdmin() || $user->isYayasan() || request()->routeIs('yayasan.*');
        
        $contract = PerformanceContract::with(['employee', 'academicYear', 'position', 'school'])->findOrFail($id);

        // Security check
        if (!$isYayasanView && $contract->school_id !== $user->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return view('admin.performance_contracts.show', compact('contract', 'isYayasanView'));
    }

    /**
     * Proses ACC atau Penolakan
     */
    public function process(Request $request, $id)
    {
        $user = auth()->user();
        $isYayasanView = $user->isSuperAdmin() || $user->isYayasan() || $request->routeIs('yayasan.*');
        $contract = PerformanceContract::findOrFail($id);

        if (!$isYayasanView && $contract->school_id !== $user->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'required_if:action,reject',
        ]);

        if ($validated['action'] === 'approve') {
            if ($isYayasanView) {
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

        return redirect()->route($isYayasanView ? 'yayasan.performance_contracts.index' : 'admin.performance_contracts.index')
            ->with('success', $msg);
    }

    /**
     * Hapus kontrak kinerja
     */
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $isYayasanView = $user->isSuperAdmin() || $user->isYayasan() || $request->routeIs('yayasan.*');
        $contract = PerformanceContract::findOrFail($id);

        if (!$isYayasanView && $contract->school_id !== $user->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        $contract->items()->delete();
        $contract->delete();

        return redirect()->route($isYayasanView ? 'yayasan.performance_contracts.index' : 'admin.performance_contracts.index')
            ->with('success', 'Perjanjian Kinerja berhasil dihapus.');
    }
}
