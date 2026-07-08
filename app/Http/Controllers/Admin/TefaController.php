<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TefaEmployee;
use App\Models\TefaAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TefaController extends Controller
{
    /**
     * Dashboard & Manajemen Karyawan TEFA (Bengkelin)
     */
    public function index(Request $request)
    {
        $today = now()->toDateString();
        
        $employees = TefaEmployee::with(['attendances' => function($q) use ($today) {
            $q->where('date', $today);
        }])->orderBy('id')->get();

        $stats = [
            'total' => $employees->count(),
            'hadir' => 0,
            'terlambat' => 0,
            'belum' => 0,
        ];

        foreach ($employees as $emp) {
            $att = $emp->attendances->first();
            if ($att) {
                if ($att->status === 'terlambat') {
                    $stats['terlambat']++;
                    $stats['hadir']++;
                } else {
                    $stats['hadir']++;
                }
            } else {
                $stats['belum']++;
            }
        }

        // Ambil log kehadiran terbaru hari ini
        $recentAttendances = TefaAttendance::with('employee')
            ->where('date', $today)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.tefa.index', compact('employees', 'stats', 'today', 'recentAttendances'));
    }

    /**
     * Tambah Karyawan TEFA
     */
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'rfid_uid' => 'nullable|string|max:100',
        ]);

        $uid = $request->filled('rfid_uid') ? strtoupper(trim($request->rfid_uid)) : null;

        if ($uid && $this->isRfidExists($uid)) {
            return back()->with('error', "Kartu RFID ($uid) sudah terdaftar pada pengguna/karyawan lain!");
        }

        TefaEmployee::create([
            'unit_name' => 'Bengkelin Tefa SMKS Pembda Nias',
            'name' => $request->name,
            'position' => $request->position,
            'phone' => $request->phone,
            'rfid_uid' => $uid,
            'is_active' => true,
        ]);

        return back()->with('success', 'Karyawan Unit Produksi TEFA berhasil ditambahkan.');
    }

    /**
     * Update Data Karyawan TEFA
     */
    public function updateEmployee(Request $request, $id)
    {
        $employee = TefaEmployee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $employee->update([
            'name' => $request->name,
            'position' => $request->position,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return back()->with('success', 'Data Karyawan TEFA berhasil diperbarui.');
    }

    /**
     * Hapus Karyawan TEFA
     */
    public function destroyEmployee($id)
    {
        $employee = TefaEmployee::findOrFail($id);
        $name = $employee->name;
        $employee->delete();

        return back()->with('success', "Karyawan TEFA ($name) berhasil dihapus.");
    }

    /**
     * Daftarkan / Update Kartu RFID Karyawan TEFA
     */
    public function registerRfid(Request $request, $id)
    {
        $request->validate([
            'rfid_uid' => 'required|string|max:100',
        ]);

        $uid = strtoupper(trim($request->rfid_uid));

        if ($this->isRfidExists($uid, $id)) {
            return back()->with('error', "Kartu RFID ($uid) sudah terdaftar pada pengguna/karyawan lain!");
        }

        $employee = TefaEmployee::findOrFail($id);
        $employee->update(['rfid_uid' => $uid]);

        return back()->with('success', "Kartu RFID ($uid) berhasil didaftarkan untuk {$employee->name}!");
    }

    /**
     * Cek apakah RFID UID sudah ada di Student, Employee, atau TefaEmployee lain
     */
    private function isRfidExists($uid, $ignoreTefaId = null)
    {
        if (\App\Models\Student::where('rfid_uid', $uid)->exists()) {
            return true;
        }
        if (\App\Models\Employee::where('rfid_uid', $uid)->exists()) {
            return true;
        }
        $tefaQuery = TefaEmployee::where('rfid_uid', $uid);
        if ($ignoreTefaId) {
            $tefaQuery->where('id', '!=', $ignoreTefaId);
        }
        return $tefaQuery->exists();
    }

    /**
     * Halaman Rekap & Riwayat Absensi TEFA
     */
    public function attendances(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        $employeeId = $request->input('tefa_employee_id');

        $employees = TefaEmployee::orderBy('id')->get();

        $query = TefaAttendance::with('employee')->orderBy('date', 'desc')->orderBy('time_in', 'desc');

        if ($request->filled('date') && !$request->filled('month')) {
            $query->where('date', $date);
        } else {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        if ($employeeId) {
            $query->where('tefa_employee_id', $employeeId);
        }

        $attendances = $query->paginate(20)->withQueryString();

        return view('admin.tefa.attendances', compact('attendances', 'employees', 'date', 'month', 'year', 'employeeId'));
    }

    /**
     * Input Absensi Manual
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'tefa_employee_id' => 'required|exists:tefa_employees,id',
            'date' => 'required|date',
            'time_in' => 'required',
            'time_out' => 'nullable',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $attendance = TefaAttendance::updateOrCreate(
            [
                'tefa_employee_id' => $request->tefa_employee_id,
                'date' => $request->date,
            ],
            [
                'time_in' => $request->time_in,
                'time_out' => $request->time_out,
                'status' => $request->status,
                'notes' => $request->notes ?? ($request->status === 'terlambat' ? 'Terlambat (Manual)' : 'Tepat Waktu (Manual)'),
                'recorded_via' => 'manual',
            ]
        );

        return back()->with('success', 'Absensi berhasil disimpan secara manual.');
    }

    /**
     * Update Absensi Manual
     */
    public function updateAttendance(Request $request, $id)
    {
        $attendance = TefaAttendance::findOrFail($id);

        $request->validate([
            'time_in' => 'required',
            'time_out' => 'nullable',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $attendance->update([
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Hapus Data Absensi
     */
    public function destroyAttendance($id)
    {
        $attendance = TefaAttendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }
}
