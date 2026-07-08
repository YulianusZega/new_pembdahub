@extends('layouts.treasurer')

@section('title', 'Laporan Progress Pembayaran')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laporan Progress Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Monitoring dan export laporan pembayaran</p>
                </div>
            </div>
            <a href="{{ route('treasurer.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300">
                Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('treasurer.reports.index') }}" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Filter Laporan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Tagihan</label>
                <select name="payment_type_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Jenis</option>
                    @foreach($paymentTypes as $type)
                    <option value="{{ $type->id }}" {{ $paymentTypeId == $type->id ? 'selected' : '' }}>
                        {{ $type->type_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="academic_year_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>
                        {{ $ay->year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <select name="classroom_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->class_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                <select name="period_type" id="periodType" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" onchange="togglePeriodFields()">
                    <option value="yearly" {{ $periodType == 'yearly' ? 'selected' : '' }}>Tahunan (Full Year)</option>
                    <option value="ytd" {{ $periodType == 'ytd' ? 'selected' : '' }}>Sampai Bulan Ini (YTD)</option>
                    <option value="month" {{ $periodType == 'month' ? 'selected' : '' }}>Bulan Tertentu</option>
                </select>
            </div>

            <div id="monthField" style="{{ $periodType != 'month' ? 'display:none' : '' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                <select name="month" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                    @endfor
                </select>
            </div>

            <div id="yearField" style="{{ $periodType != 'month' ? 'display:none' : '' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                <select name="year" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="show_all" id="showAll" value="1" {{ $showAll ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded focus:ring-2 focus:ring-emerald-500">
                <label for="showAll" class="text-sm text-gray-700 cursor-pointer">
                    Tampilkan semua termasuk siswa non-aktif (pindah/lulus/keluar)
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium">
                    🔍 Tampilkan Laporan
                </button>
                <a href="{{ route('treasurer.reports.export', request()->query()) }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium">
                    📥 Export Excel
                </a>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600 mb-2">Total Tagihan</p>
            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalBills) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600 mb-2">Total Jumlah</p>
            <h3 class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600 mb-2">Terbayar</p>
            <h3 class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600 mb-2">Tunggakan</p>
            <h3 class="text-2xl font-bold text-orange-600">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Filter Info Display -->
    @if($selectedPaymentType || $selectedAcademicYear || $selectedClassroom)
    <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-6 mb-6 border-l-4 border-emerald-500">
        <h3 class="text-lg font-bold text-gray-900 mb-3">📋 Filter Aktif</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($selectedAcademicYear)
            <div>
                <p class="text-sm text-gray-600">Tahun Ajaran</p>
                <p class="font-bold text-emerald-700">{{ $selectedAcademicYear->year }}</p>
            </div>
            @endif
            @if($selectedClassroom)
            <div>
                <p class="text-sm text-gray-600">Kelas</p>
                <p class="font-bold text-emerald-700">{{ $selectedClassroom->class_name }}</p>
            </div>
            @endif
            @if($selectedPaymentType)
            <div>
                <p class="text-sm text-gray-600">Jenis Tagihan</p>
                <p class="font-bold text-emerald-700">{{ $selectedPaymentType->type_name }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Export Button -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('treasurer.reports.export', request()->query()) }}" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export ke Excel
        </a>
    </div>

    <!-- Matrix Report: Student Rows x Month Columns -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Laporan Matrix (Siswa x Bulan)</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-3 py-3 text-left sticky left-0 bg-gray-50 z-10 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left sticky left-12 bg-gray-50 z-10 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-3 py-3 text-center border-l-2 border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jul</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Agu</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sep</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Okt</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nov</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Des</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Feb</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Mar</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Apr</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Mei</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jun</th>
                            <th class="px-4 py-3 text-center border-l-2 border-gray-200 text-xs font-semibold text-emerald-600 uppercase tracking-wider">Terbayar (Rp)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-rose-600 uppercase tracking-wider">Tunggakan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $no = 0; @endphp
                        @forelse($studentsData as $studentData)
                        @php 
                            $no++;
                            $student = $studentData['student'];
                            $classroom = $studentData['classroom'];
                            $monthlyBills = $studentData['monthly_bills'];
                            $monthOrder = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
                            $totalPaid = 0;
                            $totalUnpaid = 0;
                            
                            // Status badge color
                            $statusColors = [
                                'aktif' => 'bg-green-100 text-green-800',
                                'lulus' => 'bg-blue-100 text-blue-800',
                                'pindah' => 'bg-orange-100 text-orange-800',
                                'keluar' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $student->status != 'aktif' ? 'bg-gray-50' : '' }}">
                            <td class="px-3 py-3 text-center font-semibold sticky left-0 bg-white text-xs text-gray-500">{{ $no }}</td>
                            <td class="px-4 py-3 sticky left-12 bg-white">
                                <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                @if($student->status != 'aktif')
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $student->nisn }}</td>
                            <td class="px-4 py-3">{{ $classroom?->class_name ?? '-' }}</td>
                            
                            @foreach($monthOrder as $month)
                                @php
                                    $hasBill = isset($monthlyBills[$month]);
                                    $isPaid = $hasBill && $monthlyBills[$month]['is_paid'];
                                    if ($hasBill) {
                                        if ($isPaid) {
                                            $totalPaid++;
                                        } else {
                                            $totalUnpaid++;
                                        }
                                    }
                                @endphp
                                <td class="px-3 py-3 text-center {{ $month == 7 ? 'border-l-2 border-gray-300' : '' }}">
                                    @if($hasBill)
                                        @if($isPaid)
                                            <span class="inline-block w-6 h-6 rounded-full bg-green-100 text-green-600 font-bold leading-6">✓</span>
                                        @else
                                            <span class="inline-block w-6 h-6 rounded-full bg-red-100 text-red-600 font-bold leading-6">X</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="px-4 py-3 text-right font-bold text-emerald-600 border-l-2 border-gray-300">
                                Rp {{ number_format($studentData['total_paid'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold {{ ($studentData['total_outstanding'] ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                @if(($studentData['total_outstanding'] ?? 0) > 0)
                                    Rp {{ number_format($studentData['total_outstanding'], 0, ',', '.') }}
                                @else
                                    Lunas ✓
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="18" class="px-4 py-8 text-center text-gray-500">Tidak ada data. Silakan pilih filter dan klik "Tampilkan Laporan"</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Legend -->
            <div class="mt-4 flex items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-6 h-6 rounded-full bg-green-100 text-green-600 font-bold leading-6 text-center">✓</span>
                    <span class="text-gray-700">Sudah Bayar (Lunas)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-6 h-6 rounded-full bg-red-100 text-red-600 font-bold leading-6 text-center">X</span>
                    <span class="text-gray-700">Belum Bayar / Cicilan</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-300 font-bold">-</span>
                    <span class="text-gray-700">Tidak Ada Tagihan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePeriodFields() {
    const periodType = document.getElementById('periodType').value;
    const monthField = document.getElementById('monthField');
    const yearField = document.getElementById('yearField');
    
    if (periodType === 'month') {
        monthField.style.display = 'block';
        yearField.style.display = 'block';
    } else {
        monthField.style.display = 'none';
        yearField.style.display = 'none';
    }
}
</script>
@endsection
