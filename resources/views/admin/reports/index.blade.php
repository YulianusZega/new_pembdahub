@extends('layouts.admin')

@section('title', 'Laporan Rekap Status Tagihan Siswa')

@section('content')
<div class="space-y-6">
    {{-- ── Page Header ── --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laporan Rekap Status Tagihan</h1>
                    <p class="text-gray-600 mt-1">Monitoring siapa yang sudah & belum membayar per bulan</p>
                </div>
            </div>
            <a href="{{ route('admin.payment_reports.export', request()->query()) }}"
               class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ── Filter Form ── --}}
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-emerald-500"></i> Filter Laporan
        </h2>
        <form method="GET" action="{{ route('admin.payment_reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            @if($isSuperAdmin)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sekolah</label>
                <select name="school_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="this.form.submit()">
                    <option value="">Semua Sekolah</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Tagihan</label>
                <select name="payment_type_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Jenis</option>
                    @foreach($paymentTypes as $type)
                    <option value="{{ $type->id }}" {{ $paymentTypeId == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Ajaran</label>
                <select name="academic_year_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>{{ $ay->year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas</label>
                <select name="classroom_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Periode</label>
                <select name="period_type" id="periodType" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="togglePeriodFields()">
                    <option value="yearly" {{ $periodType == 'yearly' ? 'selected' : '' }}>Tahunan (Full Year)</option>
                    <option value="ytd"    {{ $periodType == 'ytd'    ? 'selected' : '' }}>Sampai Bulan Ini (YTD)</option>
                    <option value="month"  {{ $periodType == 'month'  ? 'selected' : '' }}>Bulan Tertentu</option>
                </select>
            </div>

            <div id="monthField" style="{{ $periodType != 'month' ? 'display:none' : '' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bulan</label>
                <select name="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>
            </div>

            <div id="yearField" style="{{ $periodType != 'month' ? 'display:none' : '' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun</label>
                <select name="year" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="show_all" id="showAll" value="1" {{ $showAll ? 'checked' : '' }}
                           class="w-4 h-4 text-emerald-600 rounded focus:ring-2 focus:ring-emerald-500">
                    <span class="text-sm text-gray-700">Tampilkan siswa non-aktif (pindah/lulus/keluar)</span>
                </label>
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('admin.payment_reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold text-sm transition-all">Reset</a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-semibold text-sm shadow-md transition-all">
                        <i class="fas fa-search mr-1.5"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-md p-5 border-l-4 border-gray-300">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Tagihan</p>
            <p class="text-3xl font-black text-gray-800">{{ number_format($totalBills) }}</p>
            <p class="text-xs text-gray-400 mt-1">item tagihan</p>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-5 border-l-4 border-blue-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Jumlah</p>
            <p class="text-2xl font-black text-blue-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">seluruh tagihan</p>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-5 border-l-4 border-emerald-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Terbayar</p>
            <p class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            @if($totalAmount > 0)
            <p class="text-xs text-emerald-500 mt-1">{{ number_format(($totalPaid / $totalAmount) * 100, 1) }}% dari total</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl shadow-md p-5 border-l-4 border-rose-400">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tunggakan</p>
            <p class="text-2xl font-black text-rose-600">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
            @if($totalAmount > 0)
            <p class="text-xs text-rose-500 mt-1">{{ number_format(($totalOutstanding / $totalAmount) * 100, 1) }}% belum terbayar</p>
            @endif
        </div>
    </div>

    {{-- ── Active Filter Info ── --}}
    @if($selectedPaymentType || $selectedSchool || $selectedClassroom)
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200 flex flex-wrap items-center gap-4">
        <span class="text-sm font-bold text-gray-600"><i class="fas fa-filter text-emerald-500 mr-1"></i> Filter Aktif:</span>
        @if($selectedSchool)
        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">🏫 {{ $selectedSchool->name }}</span>
        @endif
        @if($selectedAcademicYear)
        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">📅 {{ $selectedAcademicYear->year }}</span>
        @endif
        @if($selectedClassroom)
        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">📚 {{ $selectedClassroom->class_name }}</span>
        @endif
        @if($selectedPaymentType)
        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold">💳 {{ $selectedPaymentType->type_name }}</span>
        @endif
        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
            {{ $periodType == 'yearly' ? 'Full Tahun' : ($periodType == 'month' ? 'Bulan '.$month.'/'.$year : 'YTD s/d Bulan '.$month) }}
        </span>
        <span class="ml-auto text-sm font-bold text-gray-700">{{ $studentsData->count() }} siswa ditemukan</span>
    </div>
    @endif

    {{-- ── Matrix Table ── --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-table text-emerald-500"></i>
                Tabel Rekap Siswa × Bulan
            </h3>
            {{-- Legend --}}
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-bold">✓</span>
                    <span class="text-gray-600">Lunas</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-600 font-bold">◑</span>
                    <span class="text-gray-600">Cicilan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-600 font-bold">✗</span>
                    <span class="text-gray-600">Belum Bayar</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-gray-300 font-bold text-base">—</span>
                    <span class="text-gray-600">Tidak Ada Tagihan</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-left sticky left-0 bg-gray-50 z-10 text-xs font-bold text-gray-500 uppercase tracking-wider w-10">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[160px]">Nama Siswa</th>
                        @if($isSuperAdmin)
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sekolah</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                        {{-- Month headers Jul-Jun --}}
                        @foreach(['Jul','Agu','Sep','Okt','Nov','Des','Jan','Feb','Mar','Apr','Mei','Jun'] as $mon)
                        <th class="px-2 py-3 text-center text-xs font-bold {{ $mon == 'Jul' ? 'border-l-2 border-gray-300 text-indigo-600' : 'text-gray-500' }} uppercase tracking-wider w-10">{{ $mon }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right border-l-2 border-gray-300 text-xs font-bold text-blue-600 uppercase tracking-wider min-w-[110px]">Total Tagihan</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-emerald-600 uppercase tracking-wider min-w-[100px]">Terbayar</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-rose-600 uppercase tracking-wider min-w-[100px]">Tunggakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $no = 0; @endphp
                    @forelse($studentsData as $studentData)
                    @php
                        $no++;
                        $student      = $studentData['student'];
                        $classroom    = $studentData['classroom'];
                        $monthlyBills = $studentData['monthly_bills'];
                        $monthOrder   = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
                        $isInactive   = $student->status != 'aktif';
                        $statusColors = [
                            'lulus'  => 'bg-blue-100 text-blue-700',
                            'pindah' => 'bg-orange-100 text-orange-700',
                            'keluar' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $isInactive ? 'bg-gray-50 opacity-80' : '' }}">
                        <td class="px-3 py-3 text-center text-xs text-gray-400 font-semibold sticky left-0 {{ $isInactive ? 'bg-gray-50' : 'bg-white' }}">{{ $no }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900 leading-tight">{{ $student->full_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $student->nisn }}</p>
                            @if($isInactive)
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $statusColors[$student->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                            @endif
                        </td>
                        @if($isSuperAdmin)
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $student->school?->name ?? '-' }}</td>
                        @endif
                        <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $classroom?->class_name ?? '-' }}</td>

                        @foreach($monthOrder as $m)
                        @php
                            $hasBill = isset($monthlyBills[$m]);
                            $status  = $hasBill ? $monthlyBills[$m]['status'] : null;
                            $isPaid  = $status == 'lunas';
                            $isPartial = $status == 'cicilan';
                        @endphp
                        <td class="px-2 py-3 text-center {{ $m == 7 ? 'border-l-2 border-gray-200' : '' }}">
                            @if($hasBill)
                                @if($isPaid)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 font-bold text-sm" title="Lunas">✓</span>
                                @elseif($isPartial)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-600 font-bold text-sm" title="Cicilan">◑</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-rose-100 text-rose-600 font-bold text-sm" title="Belum Bayar">✗</span>
                                @endif
                            @else
                                <span class="text-gray-200 font-bold">—</span>
                            @endif
                        </td>
                        @endforeach

                        <td class="px-4 py-3 text-right border-l-2 border-gray-100">
                            <span class="text-sm font-semibold text-blue-700">Rp {{ number_format($studentData['total_amount'], 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($studentData['total_paid'], 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($studentData['total_outstanding'] > 0)
                            <span class="text-sm font-bold text-rose-600">Rp {{ number_format($studentData['total_outstanding'], 0, ',', '.') }}</span>
                            @else
                            <span class="text-sm font-bold text-emerald-600">Lunas ✓</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="20" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-semibold">Tidak ada data</p>
                                <p class="text-gray-400 text-sm">Pilih filter dan klik <strong>Tampilkan</strong> untuk melihat laporan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($studentsData->isNotEmpty())
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="{{ $isSuperAdmin ? 4 : 3 }}" class="px-4 py-3 text-sm font-bold text-gray-700 text-right">TOTAL ({{ $studentsData->count() }} siswa)</td>
                        @foreach(array_fill(0, 12, null) as $_)
                        <td></td>
                        @endforeach
                        <td class="px-4 py-3 text-right border-l-2 border-gray-200 font-black text-blue-700">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-black text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-black text-rose-600">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
function togglePeriodFields() {
    const periodType = document.getElementById('periodType').value;
    const monthField = document.getElementById('monthField');
    const yearField  = document.getElementById('yearField');
    if (periodType === 'month') {
        monthField.style.display = 'block';
        yearField.style.display  = 'block';
    } else {
        monthField.style.display = 'none';
        yearField.style.display  = 'none';
    }
}
</script>
@endsection
