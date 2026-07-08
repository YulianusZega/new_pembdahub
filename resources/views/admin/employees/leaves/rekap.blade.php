@extends('layouts.admin')

@section('title', 'Rekapitulasi Cuti')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.employees.leaves.index') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-sky-600 shadow-lg">
                    <i class="fas fa-chart-bar text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Rekapitulasi Cuti {{ $year }}</h1>
                    <p class="text-gray-600 mt-1">Ringkasan penggunaan cuti per pegawai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
        <form action="{{ route('admin.employees.leaves.rekap') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-36">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Tahun</label>
                <select name="year" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
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
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md transition-all"><i class="fas fa-filter text-xs mr-1"></i> Terapkan</button>
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
                        <th class="px-6 py-4 text-left text-sm font-semibold">Sekolah</th>
                        @foreach(\App\Models\EmployeeLeave::LEAVE_TYPES as $k => $v)
                        <th class="px-4 py-4 text-center text-xs font-semibold">{{ $v }}</th>
                        @endforeach
                        <th class="px-4 py-4 text-center text-sm font-semibold bg-white/10">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $index => $emp)
                    @php
                        $leavesByType = $emp->leaves->groupBy('leave_type')->map(fn($g) => $g->sum('days_count'));
                        $total = $emp->leaves->sum('days_count');
                    @endphp
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $emp->full_name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $emp->school->name ?? '-' }}</td>
                        @foreach(\App\Models\EmployeeLeave::LEAVE_TYPES as $k => $v)
                        <td class="px-4 py-3 text-center text-sm {{ ($leavesByType[$k] ?? 0) > 0 ? 'font-bold text-gray-800' : 'text-gray-300' }}">
                            {{ $leavesByType[$k] ?? 0 }}
                        </td>
                        @endforeach
                        <td class="px-4 py-3 text-center font-bold {{ $total > 0 ? 'text-blue-700' : 'text-gray-300' }}">{{ $total }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 3 + count(\App\Models\EmployeeLeave::LEAVE_TYPES) + 1 }}" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data pegawai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
