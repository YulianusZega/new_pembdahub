@extends('layouts.admin')

@section('title', 'Detail Cuti - ' . $leave->employee->full_name)

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.leaves.index') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pengajuan Cuti</h1>
                <p class="text-gray-600 mt-1">{{ $leave->employee->full_name }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-center gap-3">
            <i class="fas fa-times-circle text-red-500"></i>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Employee & Leave Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Employee Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Pegawai</h3>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400 to-sky-500 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($leave->employee->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900">{{ $leave->employee->full_name }}</div>
                        <div class="text-gray-500">{{ $leave->employee->employee_code }} · {{ $leave->employee->school->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Detail Cuti</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-400 font-bold uppercase">Jenis</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $leave->leave_type_label }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-400 font-bold uppercase">Tanggal Mulai</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $leave->start_date->format('d M Y') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-400 font-bold uppercase">Tanggal Selesai</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $leave->end_date->format('d M Y') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-400 font-bold uppercase">Jumlah Hari</p>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ $leave->days_count }}</p>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-400 font-bold uppercase">Alasan</p>
                    <p class="mt-1 text-gray-800">{{ $leave->reason }}</p>
                </div>
                @if($leave->attachment)
                <div class="mt-4">
                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors">
                        <i class="fas fa-paperclip"></i> Lihat Lampiran
                    </a>
                </div>
                @endif
                @if($leave->notes)
                <div class="mt-4 p-4 bg-yellow-50 rounded-xl">
                    <p class="text-xs text-gray-400 font-bold uppercase">Catatan</p>
                    <p class="mt-1 text-gray-800">{{ $leave->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Status & Timeline -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Status</h3>
                @php
                    $colors = ['pending' => 'yellow', 'approved_kepsek' => 'blue', 'approved' => 'green', 'rejected' => 'red'];
                    $c = $colors[$leave->status] ?? 'gray';
                @endphp
                <div class="text-center py-4">
                    <span class="inline-flex items-center gap-2 px-6 py-3 bg-{{ $c }}-100 text-{{ $c }}-800 text-lg font-bold rounded-xl">
                        @if($leave->status === 'approved') <i class="fas fa-check-circle"></i>
                        @elseif($leave->status === 'rejected') <i class="fas fa-times-circle"></i>
                        @else <i class="fas fa-clock"></i>
                        @endif
                        {{ $leave->status_label }}
                    </span>
                    @if($leave->needsYayasanApproval())
                    <p class="text-xs text-gray-500 mt-2">Memerlukan persetujuan Yayasan (>3 hari)</p>
                    @endif
                </div>
            </div>

            <!-- Approval Timeline -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Timeline Approval</h3>
                <div class="space-y-4">
                    <!-- Step 1: Submitted -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white"><i class="fas fa-check text-xs"></i></div>
                            <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-800">Diajukan</p>
                            <p class="text-xs text-gray-500">{{ $leave->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <!-- Step 2: Kepsek -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            @if($leave->approved_by_kepsek)
                            <div class="w-8 h-8 rounded-full {{ $leave->status === 'rejected' && !$leave->approved_by_yayasan ? 'bg-red-500' : 'bg-green-500' }} flex items-center justify-center text-white">
                                <i class="fas {{ $leave->status === 'rejected' && !$leave->approved_by_yayasan ? 'fa-times' : 'fa-check' }} text-xs"></i>
                            </div>
                            @else
                            <div class="w-8 h-8 rounded-full {{ $leave->status === 'pending' ? 'bg-yellow-400 animate-pulse' : 'bg-gray-300' }} flex items-center justify-center text-white"><i class="fas fa-hourglass text-xs"></i></div>
                            @endif
                            @if($leave->needsYayasanApproval())
                            <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                            @endif
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-800">Kepala Sekolah</p>
                            @if($leave->approvedByKepsek)
                            <p class="text-xs text-gray-500">{{ $leave->approvedByKepsek->name }} · {{ $leave->approved_at_kepsek?->format('d M Y, H:i') }}</p>
                            @else
                            <p class="text-xs text-yellow-600">Menunggu persetujuan</p>
                            @endif
                        </div>
                    </div>
                    <!-- Step 3: Yayasan (only if > 3 days) -->
                    @if($leave->needsYayasanApproval())
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            @if($leave->approved_by_yayasan)
                            <div class="w-8 h-8 rounded-full {{ $leave->status === 'rejected' ? 'bg-red-500' : 'bg-green-500' }} flex items-center justify-center text-white">
                                <i class="fas {{ $leave->status === 'rejected' ? 'fa-times' : 'fa-check' }} text-xs"></i>
                            </div>
                            @else
                            <div class="w-8 h-8 rounded-full {{ $leave->status === 'approved_kepsek' ? 'bg-yellow-400 animate-pulse' : 'bg-gray-300' }} flex items-center justify-center text-white"><i class="fas fa-hourglass text-xs"></i></div>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Ketua Yayasan</p>
                            @if($leave->approvedByYayasan)
                            <p class="text-xs text-gray-500">{{ $leave->approvedByYayasan->name }} · {{ $leave->approved_at_yayasan?->format('d M Y, H:i') }}</p>
                            @elseif($leave->status === 'approved_kepsek')
                            <p class="text-xs text-yellow-600">Menunggu persetujuan</p>
                            @else
                            <p class="text-xs text-gray-400">Belum sampai tahap ini</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @if($leave->status === 'rejected' && $leave->rejection_reason)
                <div class="mt-4 p-4 bg-red-50 rounded-xl">
                    <p class="text-xs text-red-400 font-bold uppercase">Alasan Penolakan</p>
                    <p class="mt-1 text-red-800">{{ $leave->rejection_reason }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            @if($canApprove)
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6 space-y-3">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Tindakan</h3>
                <form action="{{ route('admin.employees.leaves.approve', $leave) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all flex items-center justify-center gap-2"
                        onclick="return confirm('Setujui pengajuan cuti ini?')">
                        <i class="fas fa-check-circle"></i> Setujui
                    </button>
                </form>
                <button onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                    class="w-full px-4 py-3 bg-white border-2 border-red-200 text-red-600 rounded-xl font-semibold hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-times-circle"></i> Tolak
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($canApprove)
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tolak Pengajuan Cuti</h3>
        <form action="{{ route('admin.employees.leaves.reject', $leave) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan *</label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-all">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
