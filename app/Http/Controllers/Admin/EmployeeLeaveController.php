<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = EmployeeLeave::with(['employee.school', 'approvedByKepsek', 'approvedByYayasan'])
            ->orderByRaw("FIELD(status, 'pending', 'approved_kepsek', 'approved', 'rejected')")
            ->latest();

        // School scope
        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $query->where('school_id', $user->school_id);
        } elseif ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
        }

        $leaves = $query->paginate(15)->withQueryString();

        // Stats
        $statsBase = EmployeeLeave::query();
        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $statsBase->where('school_id', $user->school_id);
        }
        $stats = [
            'pending' => (clone $statsBase)->where('status', 'pending')->count(),
            'needs_yayasan' => (clone $statsBase)->where('status', 'approved_kepsek')->count(),
            'approved_month' => (clone $statsBase)->where('status', 'approved')->whereMonth('updated_at', now()->month)->count(),
            'rejected' => (clone $statsBase)->where('status', 'rejected')->whereMonth('updated_at', now()->month)->count(),
        ];

        $schools = $user->isSuperAdmin() || $user->isKetuaYayasan()
            ? School::orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.employees.leaves.index', compact('leaves', 'stats', 'schools'));
    }

    public function create()
    {
        $user = auth()->user();

        $employees = Employee::where('is_active', true)
            ->when(!$user->isSuperAdmin() && !$user->isKetuaYayasan(), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('full_name')
            ->get();

        $leaveTypes = EmployeeLeave::LEAVE_TYPES;

        return view('admin.employees.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:' . implode(',', array_keys(EmployeeLeave::LEAVE_TYPES)),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

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

        return redirect()->route('admin.employees.leaves.index')
            ->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    public function show(EmployeeLeave $leave)
    {
        $leave->load(['employee.school', 'approvedByKepsek', 'approvedByYayasan']);
        $canApprove = $leave->canBeApprovedBy(auth()->user());

        return view('admin.employees.leaves.show', compact('leave', 'canApprove'));
    }

    public function approve(EmployeeLeave $leave)
    {
        $user = auth()->user();

        if (!$leave->canBeApprovedBy($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menyetujui cuti ini.');
        }

        $leave->approve($user);

        $message = $leave->status === 'approved'
            ? 'Cuti berhasil disetujui.'
            : 'Cuti disetujui oleh Kepala Sekolah. Menunggu persetujuan Yayasan.';

        return back()->with('success', $message);
    }

    public function reject(Request $request, EmployeeLeave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = auth()->user();

        if (!$leave->canBeApprovedBy($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menolak cuti ini.');
        }

        $leave->reject($user, $request->rejection_reason);

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }

    public function rekap(Request $request)
    {
        $user = auth()->user();
        $year = $request->get('year', now()->year);

        $query = Employee::with(['leaves' => fn($q) => $q->where('status', 'approved')->whereYear('start_date', $year)])
            ->where('is_active', true);

        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $query->where('school_id', $user->school_id);
        } elseif ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        $employees = $query->orderBy('full_name')->get();

        $schools = $user->isSuperAdmin() || $user->isKetuaYayasan()
            ? School::orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.employees.leaves.rekap', compact('employees', 'schools', 'year'));
    }
}
