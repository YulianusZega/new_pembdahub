@extends('layouts.admin')

@section('title', 'Cuti & Izin Pegawai')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-sky-600 shadow-lg">
                    <i class="fas fa-calendar-check text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Cuti & Izin Pegawai</h1>
                    <p class="text-gray-600 mt-1">Kelola pengajuan cuti dan izin pegawai</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.employees.leaves.rekap') }}"
                    class="flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                    <i class="fas fa-chart-bar"></i> Rekapitulasi
                </a>
                <a href="{{ route('admin.employees.leaves.create') }}"
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-600 to-sky-700 text-white rounded-xl font-medium hover:from-cyan-700 hover:to-sky-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-plus"></i> Ajukan Cuti
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-center gap-3">
            <i class="fas fa-times-circle text-red-500"></i>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-yellow-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center"><i class="fas fa-clock text-yellow-500 text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Perlu Yayasan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['needs_yayasan'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center"><i class="fas fa-building text-blue-500 text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Disetujui (Bulan Ini)</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['approved_month'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center"><i class="fas fa-check-circle text-green-500 text-lg"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ditolak (Bulan Ini)</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center"><i class="fas fa-times-circle text-red-500 text-lg"></i></div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
        <form action="{{ route('admin.employees.leaves.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Cari Pegawai</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pegawai..."
                    class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
            </div>
            <div class="w-full md:w-44">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\EmployeeLeave::STATUSES as $k => $v)
                    <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-44">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Jenis Cuti</label>
                <select name="leave_type" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    <option value="">Semua Jenis</option>
                    @foreach(\App\Models\EmployeeLeave::LEAVE_TYPES as $k => $v)
                    <option value="{{ $k }}" {{ request('leave_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isKetuaYayasan())
            <div class="w-full md:w-48">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Unit Sekolah</label>
                <select name="school_id" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    <option value="">Semua Unit</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Terapkan
                </button>
                @if(request()->anyFilled(['search', 'status', 'leave_type', 'school_id']))
                <a href="{{ route('admin.employees.leaves.index') }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-bold transition-all">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Pegawai</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Hari</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $index => $leave)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-100 text-cyan-700 font-bold text-sm">
                                {{ $leaves->firstItem() + $index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-sky-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($leave->employee->full_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $leave->employee->full_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $leave->employee->school->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">{{ $leave->leave_type_label }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-gray-800">{{ $leave->days_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $colors = ['pending' => 'yellow', 'approved_kepsek' => 'blue', 'approved' => 'green', 'rejected' => 'red'];
                                $c = $colors[$leave->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-{{ $c }}-100 text-{{ $c }}-800 text-xs font-semibold rounded-full">
                                {{ $leave->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.employees.leaves.show', $leave) }}"
                                class="p-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors inline-block" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-calendar-xmark text-4xl text-gray-300"></i>
                                <p class="text-gray-600 font-medium">Belum ada pengajuan cuti</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $leaves->links() }}</div>
        @endif
    </div>
</div>
@endsection
