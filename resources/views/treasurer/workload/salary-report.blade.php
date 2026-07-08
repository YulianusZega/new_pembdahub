@extends('layouts.treasurer')
@section('title', 'Laporan Gaji')
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Gaji</h1>
                    <p class="text-gray-600 mt-1">Rekapitulasi penggajian pegawai per sekolah</p>
                </div>
            </div>
            <div class="flex gap-3">
                @if($schoolId)
                <a href="{{ route('treasurer.salary-report.export', ['academic_year_id' => $yearId, 'semester_id' => $semesterId]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-file-csv mr-2"></i> Export CSV
                </a>
                @endif
                <a href="{{ route('treasurer.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl"><p class="text-red-700">{{ session('error') }}</p></div>
    @endif

    {{-- Filter & Stats --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <form method="GET" class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sekolah</label>
                    <input type="text" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-500 font-medium" value="{{ $school->name }}" readonly disabled>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $yearId == $ay->id ? 'selected' : '' }}>{{ $ay->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Semester</label>
                    <select name="semester_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>{{ $sem->semester_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition text-sm font-semibold shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Tampilkan
                    </button>
                </div>
            </form>

            @if($schoolId)
            <div class="flex items-center gap-6 lg:border-l lg:pl-6 h-full">
                <div class="text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Pegawai</div>
                    <div class="text-xl font-bold text-gray-900">{{ $employees->count() }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider">Total THP (Seluruh Unit)</div>
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalGaji, 0, ',', '.') }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($schoolId)
    <!-- Report Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-gray-500 text-[11px] font-bold uppercase tracking-wider text-center">
                        <th class="px-3 py-4 text-left w-12">No</th>
                        <th class="px-4 py-4 text-left">Nama Pegawai / Status</th>
                        <th class="px-3 py-4 text-right">Gaji Pokok</th>
                        <th class="px-4 py-4 text-left">Tunjangan Jabatan</th>
                        <th class="px-3 py-4 text-right">Honor Mengajar</th>
                        <th class="px-5 py-4 text-left">Tunjangan Yayasan</th>
                        <th class="px-4 py-4 text-right">THP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($employees as $idx => $emp)
                    @php $sal = $salaryData[$emp->id] ?? []; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-3 py-4 text-xs text-gray-400 text-center font-medium align-top">{{ $idx + 1 }}</td>
                        <td class="px-4 py-4 align-top">
                            <div class="font-bold text-gray-900 leading-tight text-[13px]">{{ $emp->full_name }}</div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-gray-500 font-mono">{{ $emp->employee_code ?? '-' }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                @php
                                    $statusClasses = [
                                        'yayasan' => 'text-emerald-600',
                                        'pns' => 'text-blue-600',
                                        'honorer' => 'text-amber-600',
                                        'kontrak' => 'text-purple-600',
                                    ];
                                    $statusColor = $statusClasses[$emp->employment_status ?? ''] ?? 'text-gray-500';
                                @endphp
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ $statusColor }}">{{ $emp->employment_status ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-right text-xs font-medium text-gray-700 align-top pt-5">Rp {{ number_format($sal['gaji_pokok'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 align-top">
                            <div class="space-y-1">
                                @if(!empty($sal['jabatan_details']))
                                    @foreach($sal['jabatan_details'] as $detail)
                                        <div class="flex justify-between items-start gap-4 text-[10px] leading-tight">
                                            <span class="text-gray-600 font-medium">• {{ $detail['name'] }}</span>
                                            <span class="text-gray-900 font-bold whitespace-nowrap">Rp {{ number_format($detail['amount'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-gray-400 text-[10px] italic">-</div>
                                @endif
                                
                                <div class="text-right border-t border-gray-100 pt-1 mt-1 font-bold text-indigo-700 text-[11px]">
                                    Rp {{ number_format($sal['tunjangan_jabatan'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-right text-xs font-bold text-gray-900 italic align-top pt-5">Rp {{ number_format($sal['honor_mengajar'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 align-top">
                            <div class="space-y-0.5">
                                @if(($sal['tunjangan_keluarga'] ?? 0) > 0) 
                                    <div class="flex justify-between text-[10px] font-medium leading-tight text-pink-600 italic">
                                        <span>Keluarga</span> 
                                        <span>Rp {{ number_format($sal['tunjangan_keluarga'], 0, ',', '.') }}</span>
                                    </div> 
                                @endif
                                @if(($sal['tunjangan_anak'] ?? 0) > 0) 
                                    <div class="flex justify-between text-[10px] font-medium leading-tight text-blue-600 italic">
                                        <span>Anak</span> 
                                        <span>Rp {{ number_format($sal['tunjangan_anak'], 0, ',', '.') }}</span>
                                    </div> 
                                @endif
                                @if(($sal['tunjangan_beras'] ?? 0) > 0) 
                                    <div class="flex justify-between text-[10px] font-medium leading-tight text-amber-600 italic">
                                        <span>Beras</span> 
                                        <span>Rp {{ number_format($sal['tunjangan_beras'], 0, ',', '.') }}</span>
                                    </div> 
                                @endif
                            </div>
                            @php $totalYayasan = (($sal['tunjangan_keluarga'] ?? 0) + ($sal['tunjangan_anak'] ?? 0) + ($sal['tunjangan_beras'] ?? 0)); @endphp
                            @if($totalYayasan > 0)
                            <div class="text-[11px] font-bold text-emerald-700 mt-1 border-t border-emerald-100 pt-1 text-right">
                                Rp {{ number_format($totalYayasan, 0, ',', '.') }}
                            </div>
                            @else
                            <div class="text-[11px] text-gray-400 italic text-center pt-1">-</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right align-top pt-5">
                            <div class="text-[15px] font-bold text-blue-800 tracking-tight">Rp {{ number_format($sal['thp'] ?? 0, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 italic font-medium">Data gaji belum tersedia untuk unit ini.</td></tr>
                    @endforelse
                </tbody>
                @if($employees->count() > 0)
                <tfoot class="bg-gray-50 border-t-2 border-gray-100 font-bold text-[12px] uppercase">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-gray-500">TOTAL SELURUH UNIT</td>
                        <td class="px-4 py-4 text-right text-gray-900 tracking-tighter">Rp {{ number_format(collect($salaryData)->sum('tunjangan_jabatan'), 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-gray-900 tracking-tighter">Rp {{ number_format(collect($salaryData)->sum('honor_mengajar'), 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right text-emerald-800 tracking-tighter">
                            Rp {{ number_format(collect($salaryData)->sum('tunjangan_keluarga') + collect($salaryData)->sum('tunjangan_anak') + collect($salaryData)->sum('tunjangan_beras'), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-blue-900 text-lg tracking-tighter">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
