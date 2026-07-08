<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeLeaveController extends Controller
{


    /**
     * Resolve employee profile of the logged-in user
     */
    private function getEmployee(): Employee
    {
        $user = auth()->user();
        $employee = $user->employee;
        
        if (!$employee && $user->teacher) {
            $employee = $user->teacher->employee;
        }

        if (!$employee) {
            abort(404, 'Profil kepegawaian Anda tidak ditemukan.');
        }

        return $employee;
    }

    /**
     * Display a listing of leave requests
     */
    public function index()
    {
        $employee = $this->getEmployee();
        $leaves = EmployeeLeave::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guru.leaves.index', compact('leaves', 'employee'));
    }

    /**
     * Show create leave request form
     */
    public function create()
    {
        $employee = $this->getEmployee();
        $leaveTypes = EmployeeLeave::LEAVE_TYPES;

        return view('guru.leaves.create', compact('leaveTypes', 'employee'));
    }

    /**
     * Store new leave request
     */
    public function store(Request $request)
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'leave_type' => 'required|in:' . implode(',', array_keys(EmployeeLeave::LEAVE_TYPES)),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ], [
            'leave_type.required' => 'Jenis cuti wajib dipilih.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'reason.required' => 'Alasan cuti wajib diisi.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 5MB.',
            'attachment.mimes' => 'Format berkas lampiran harus berupa PDF, JPG, JPEG, atau PNG.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('employee_leaves', 'public');
        }

        EmployeeLeave::create([
            'employee_id' => $employee->id,
            'school_id' => $employee->school_id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('guru.leaves.index')
            ->with('success', 'Pengajuan cuti Anda berhasil diajukan dan sedang menunggu persetujuan.');
    }
}
