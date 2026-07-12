@extends('layouts.treasurer')

@section('title', 'Slip Gaji - PembdaHUB')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-file-invoice-dollar text-sm"></i>
            </div>
            Slip Gaji Pegawai
        </h2>
        <p class="text-sm text-gray-500 mt-1">Cari pegawai untuk melihat detail atau mencetak slip gaji</p>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sekolah</label>
                <input type="text" class="w-full border border-gray-300 bg-gray-50 text-gray-500 rounded-xl px-3 py-2.5 text-sm cursor-not-allowed font-medium" value="{{ $school->name }}" disabled>
                <input type="hidden" name="school_id" value="{{ $schoolId }}">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun Ajaran</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $yearId == $ay->id ? 'selected' : '' }}>{{ $ay->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Semester</label>
                <select name="semester_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>{{ $sem->semester_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Pegawai</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Nama atau kode pegawai..."
                        class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($employees->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-1">Tidak Ada Data</h3>
            <p class="text-sm text-gray-500">Tidak ditemukan pegawai dengan filter yang dipilih</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Pegawai</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Jabatan</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Gaji Pokok</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employees as $emp)
                        <tr class="hover:bg-emerald-50/30 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($emp->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $emp->full_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $emp->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $statusColors = [
                                        'yayasan' => 'bg-emerald-100 text-emerald-700',
                                        'pns' => 'bg-blue-100 text-blue-700',
                                        'honorer' => 'bg-amber-100 text-amber-700',
                                        'kontrak' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $color = $statusColors[$emp->employment_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($emp->employment_status ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600 text-sm">
                                @if($emp->activePositions && $emp->activePositions->isNotEmpty())
                                    {{ $emp->activePositions->pluck('position_name')->join(', ') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-gray-700 text-sm">
                                Rp {{ number_format($emp->basic_salary ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('treasurer.salary-detail', ['employee' => $emp->id, 'academic_year_id' => $yearId, 'semester_id' => $semesterId]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition"
                                       title="Lihat Detail Gaji">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('treasurer.salary-slip', ['employee' => $emp->id, 'academic_year_id' => $yearId, 'semester_id' => $semesterId]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg text-xs font-semibold transition"
                                       title="Cetak Slip Gaji" target="_blank">
                                        <i class="fas fa-print"></i> Slip
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($employees->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
