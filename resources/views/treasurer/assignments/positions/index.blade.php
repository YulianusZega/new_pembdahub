@extends('layouts.treasurer')

@section('title', 'Penugasan Jabatan & Tunjangan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Penugasan Jabatan & Tunjangan</h1>
                    <p class="text-gray-600 mt-1">Daftar penugasan jabatan guru beserta nilai tunjangan di unit {{ $school->name ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Information -->
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-emerald-800 font-bold mb-1">Informasi</p>
                <p class="text-emerald-700 text-sm">Halaman ini bersifat <strong>Read-Only</strong> (hanya lihat). Pengelolaan dan penunjukan jabatan dilakukan oleh Admin Sekolah, sedangkan pembayaran tunjangan dikelola oleh Bendahara.</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('treasurer.assignments.positions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran</label>
                <select name="academic_year_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->year }} {{$year->is_active ? '(Aktif)' : ''}}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-1"></i> Cari</label>
                <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Nama/Kode Guru..." value="{{ request('search') }}">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-green-700 shadow-md hover:shadow-lg transition-all">
                    Filter
                </button>
                <a href="{{ route('treasurer.assignments.positions.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold w-14">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold">Nama & Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold">Jabatan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold">Total Tunjangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $index => $employee)
                        @php
                            $totalAllowance = 0;
                            $positions = $employee->employeePositions;
                        @endphp
                        <tr class="hover:bg-emerald-50/20 transition-colors">
                            <td class="px-4 py-3">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 font-bold text-xs">
                                    {{ $employees->firstItem() + $index }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" 
                                    class="w-10 h-10 rounded-xl object-cover border-2 border-emerald-200">
                                @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $employee->full_name }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded">
                                            {{ $employee->employee_code }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($positions->isNotEmpty())
                                    <div class="space-y-2">
                                        @foreach($positions as $empPos)
                                            @php
                                                $totalAllowance += $empPos->position->allowance_amount ?? 0;
                                            @endphp
                                            <div class="flex items-center gap-2 px-3 py-2 bg-white border-l-4 {{ $empPos->is_primary ? 'border-emerald-600 bg-gradient-to-r from-emerald-50 to-green-50' : 'border-gray-300 bg-gray-50' }} rounded-r-lg shadow-sm hover:shadow-md transition-shadow group">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        @if($empPos->is_primary)
                                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        @endif
                                                        <span class="font-bold text-sm {{ $empPos->is_primary ? 'text-emerald-700' : 'text-gray-700' }}">
                                                            {{ $empPos->position->display_name ?? '-' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        @if($empPos->classroom_id)
                                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded flex items-center gap-1">
                                                            <i class="fas fa-school mr-1"></i> {{ $empPos->classroom->class_name ?? '' }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right flex items-center gap-3">
                                                    <div>
                                                        <div class="text-xs text-gray-500">Tunjangan</div>
                                                        <div class="font-bold text-emerald-600">
                                                            Rp {{ number_format($empPos->position->allowance_amount ?? 0, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                        <span class="text-gray-400"><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i></span>
                                        <span class="text-gray-500 text-sm italic">Belum ada penugasan</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-bold text-sm {{ $totalAllowance > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                                    Rp {{ number_format($totalAllowance, 0, ',', '.') }}
                                </div>
                                @if($totalAllowance > 0)
                                <span class="text-xs text-gray-400">/bulan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 italic">
                                Tidak ada data guru untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
