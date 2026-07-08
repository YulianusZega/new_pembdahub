@extends('layouts.admin')

@section('title', 'Rekapitulasi Absensi Pegawai')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.employees.attendance.index') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg">
                    <i class="fas fa-chart-bar text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Rekapitulasi Absensi</h1>
                    <p class="text-gray-600 mt-1">Rekap kehadiran bulanan pegawai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
        <form action="{{ route('admin.employees.attendance.rekap') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-36">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Bulan</label>
                <select name="month" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full md:w-28">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Tahun</label>
                <select name="year" class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full md:w-56">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Unit Sekolah</label>
                <select name="school_id" required class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all appearance-none">
                    <option value="">Pilih Unit</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md transition-all"><i class="fas fa-filter text-xs mr-1"></i> Tampilkan</button>
        </form>
    </div>

    @if($schoolId && $employees->count())
    <!-- Legend -->
    <div class="flex flex-col gap-3 px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-2xl shadow-sm">
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-3 bg-teal-500 rounded-full"></span>
                <span class="text-xs font-bold text-gray-700">Keterangan Umum (Izin/Sakit):</span>
            </div>
            <div class="flex flex-wrap gap-3">
                @php
                    $legend = ['S' => ['Sakit', 'yellow'], 'I' => ['Izin', 'blue'], 'D' => ['Dinas', 'purple'], 'C' => ['Cuti', 'indigo']];
                @endphp
                @foreach($legend as $code => [$label, $color])
                <span class="flex items-center gap-1.5 text-xs">
                    <span class="w-6 h-6 rounded bg-{{ $color }}-100 text-{{ $color }}-700 border border-{{ $color }}-200 flex items-center justify-center font-bold text-[10px] shadow-sm">{{ $code }}</span>
                    <span class="text-gray-500 font-medium">{{ $label }}</span>
                </span>
                @endforeach
            </div>
        </div>
        <div class="border-t border-gray-200/60 pt-2 flex flex-wrap items-center gap-x-6 gap-y-2">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-3 bg-indigo-500 rounded-full"></span>
                <span class="text-xs font-bold text-gray-700">Kehadiran (Guru & Staf):</span>
            </div>
            <div class="flex flex-wrap gap-4">
                <span class="flex items-center gap-1.5 text-xs">
                    <span class="w-10 h-6 rounded bg-emerald-500 text-white flex items-center justify-center font-bold text-[10px] shadow-md">H / HM</span>
                    <span class="text-gray-500 font-medium">Hadir Wajib (Guru: Sesuai Jadwal | Staf: Senin s.d. Jumat)</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs">
                    <span class="w-8 h-6 rounded bg-indigo-500 text-white flex items-center justify-center font-bold text-[10px] shadow-md">TK</span>
                    <span class="text-gray-500 font-medium">Tugas Khusus (Hadir di luar hari wajib, +15 Point)</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs">
                    <span class="w-6 h-6 rounded bg-rose-500 text-white flex items-center justify-center font-bold text-[10px] shadow-md">A</span>
                    <span class="text-gray-500 font-medium">Alpha (Tidak hadir di hari wajib)</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs">
                    <span class="text-gray-400 font-bold text-[12px] px-1">-</span>
                    <span class="text-gray-500 font-medium">Bebas Tugas / Hari Libur</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-blue-50/80">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-gradient-to-r from-teal-500 to-emerald-600 text-white shadow-sm">
                        <th class="px-4 py-3.5 text-left font-semibold sticky left-0 bg-teal-600 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Pegawai</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                        @php $dayName = \Carbon\Carbon::create($year, $month, $d)->format('D'); @endphp
                        <th class="px-1 py-3.5 text-center font-semibold w-8 {{ in_array($dayName, ['Sat', 'Sun']) ? 'bg-white/10' : '' }}">
                            <div>{{ $d }}</div>
                            <div class="text-[9px] opacity-70">{{ substr($dayName, 0, 2) }}</div>
                        </th>
                        @endfor
                        <th class="px-2 py-3.5 text-center font-semibold bg-white/10">H</th>
                        <th class="px-2 py-3.5 text-center font-semibold bg-white/10">S</th>
                        <th class="px-2 py-3.5 text-center font-semibold bg-white/10">I</th>
                        <th class="px-2 py-3.5 text-center font-semibold bg-white/10">A</th>
                        <th class="px-2 py-3.5 text-center font-semibold bg-white/10">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($employees as $emp)
                    @php
                        $isTeacher = $emp->isTeacher();
                        $teachingDays = [];
                        if ($isTeacher && $emp->teacher) {
                            $teachingDays = $emp->teacher->schedules->pluck('day_of_week')->unique()->toArray();
                        }

                        $data = $attendanceData[$emp->id] ?? [];
                        $totals = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0, 'tugas_khusus' => 0];
                        
                        if ($isTeacher) {
                            $totalScheduledDays = 0;
                            $presentOnScheduled = 0;
                            
                            for($d = 1; $d <= $daysInMonth; $d++) {
                                $dateObj = \Carbon\Carbon::create($year, $month, $d);
                                $dayOfWeekName = strtolower($dateObj->format('l'));
                                $isScheduled = in_array($dayOfWeekName, $teachingDays);
                                
                                if ($isScheduled) {
                                    $totalScheduledDays++;
                                }
                                
                                $att = $data[$d] ?? null;
                                if ($att) {
                                    if ($att->status === 'hadir') {
                                        if ($isScheduled) {
                                            $totals['hadir']++;
                                            $presentOnScheduled++;
                                        } else {
                                            $totals['tugas_khusus']++;
                                        }
                                    } elseif (in_array($att->status, ['sakit', 'izin', 'cuti', 'dinas_luar'])) {
                                        if (isset($totals[$att->status])) {
                                            $totals[$att->status]++;
                                        }
                                    } elseif ($att->status === 'alpha') {
                                        if ($isScheduled) {
                                            $totals['alpha']++;
                                        }
                                    }
                                } else {
                                    if ($isScheduled) {
                                        $totals['alpha']++;
                                    }
                                }
                            }
                            $pct = $totalScheduledDays > 0 ? round(($presentOnScheduled / $totalScheduledDays) * 100) : 0;
                        } else {
                            $totalScheduledDays = 0;
                            $presentOnScheduled = 0;
                            
                            for($d = 1; $d <= $daysInMonth; $d++) {
                                $dateObj = \Carbon\Carbon::create($year, $month, $d);
                                $dn = $dateObj->format('D');
                                $isWeekend = in_array($dn, ['Sat', 'Sun']);
                                
                                if (!$isWeekend) {
                                    $totalScheduledDays++;
                                }
                                
                                $att = $data[$d] ?? null;
                                if ($att) {
                                    if ($att->status === 'hadir') {
                                        if (!$isWeekend) {
                                            $totals['hadir']++;
                                            $presentOnScheduled++;
                                        } else {
                                            $totals['tugas_khusus']++;
                                        }
                                    } elseif (in_array($att->status, ['sakit', 'izin', 'cuti', 'dinas_luar'])) {
                                        if (isset($totals[$att->status])) {
                                            $totals[$att->status]++;
                                        }
                                    } elseif ($att->status === 'alpha') {
                                        if (!$isWeekend) {
                                            $totals['alpha']++;
                                        }
                                    }
                                } else {
                                    if (!$isWeekend) {
                                        $totals['alpha']++;
                                    }
                                }
                            }
                            $pct = $totalScheduledDays > 0 ? round(($presentOnScheduled / $totalScheduledDays) * 100) : 0;
                        }
                    @endphp
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-3 py-2 font-medium text-gray-800 whitespace-nowrap sticky left-0 bg-white z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                            {{ $emp->full_name }}
                            @if($isTeacher)
                            <span class="block text-[9px] text-teal-600 font-semibold mt-0.5"><i class="fas fa-chalkboard-teacher mr-0.5"></i> Guru</span>
                            @else
                            <span class="block text-[9px] text-slate-500 font-medium mt-0.5"><i class="fas fa-user-cog mr-0.5"></i> Staf</span>
                            @endif
                        </td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dateObj = \Carbon\Carbon::create($year, $month, $d);
                            $isWeekend = in_array($dateObj->format('D'), ['Sat', 'Sun']);
                            $dayOfWeekName = strtolower($dateObj->format('l'));
                            $att = $data[$d] ?? null;
                            
                            $cellText = '-';
                            $cellBg = 'text-gray-300';
                            
                            if ($isTeacher) {
                                $isScheduled = in_array($dayOfWeekName, $teachingDays);
                                if ($att) {
                                    if ($att->status === 'hadir') {
                                        if ($isScheduled) {
                                            $cellText = 'HM';
                                            $cellBg = 'bg-emerald-500 text-white font-bold shadow-sm';
                                        } else {
                                            $cellText = 'TK';
                                            $cellBg = 'bg-indigo-500 text-white font-bold shadow-sm';
                                        }
                                    } else {
                                        $map = [
                                            'sakit' => ['S', 'bg-yellow-100 text-yellow-700 border border-yellow-300'],
                                            'izin' => ['I', 'bg-blue-100 text-blue-700 border border-blue-300'],
                                            'alpha' => ['A', 'bg-rose-500 text-white font-bold shadow-sm'],
                                            'dinas_luar' => ['D', 'bg-purple-100 text-purple-700 border border-purple-300'],
                                            'cuti' => ['C', 'bg-indigo-100 text-indigo-700 border border-indigo-300'],
                                        ];
                                        $cellInfo = $map[$att->status] ?? ['-', 'text-gray-300'];
                                        $cellText = $cellInfo[0];
                                        $cellBg = $cellInfo[1];
                                    }
                                } else {
                                    if ($isScheduled) {
                                        $cellText = 'A';
                                        $cellBg = 'bg-rose-500 text-white font-bold shadow-sm';
                                    }
                                }
                            } else {
                                $st = $att ? $att->status : null;
                                if ($att) {
                                    if ($att->status === 'hadir') {
                                        if (!$isWeekend) {
                                            $cellText = 'H';
                                            $cellBg = 'bg-emerald-500 text-white font-bold shadow-sm';
                                        } else {
                                            $cellText = 'TK';
                                            $cellBg = 'bg-indigo-500 text-white font-bold shadow-sm';
                                        }
                                    } else {
                                        $map = [
                                            'sakit' => ['S', 'bg-yellow-100 text-yellow-700 border border-yellow-300'],
                                            'izin' => ['I', 'bg-blue-100 text-blue-700 border border-blue-300'],
                                            'alpha' => ['A', 'bg-rose-500 text-white font-bold shadow-sm'],
                                            'dinas_luar' => ['D', 'bg-purple-100 text-purple-700 border border-purple-300'],
                                            'cuti' => ['C', 'bg-indigo-100 text-indigo-700 border border-indigo-300'],
                                        ];
                                        $cellInfo = $map[$att->status] ?? ['-', 'text-gray-300'];
                                        $cellText = $cellInfo[0];
                                        $cellBg = $cellInfo[1];
                                    }
                                } else {
                                    if (!$isWeekend) {
                                        $cellText = 'A';
                                        $cellBg = 'bg-rose-500 text-white font-bold shadow-sm';
                                    }
                                }
                            }
                        @endphp
                        <td class="px-0.5 py-2 text-center {{ $isWeekend ? 'bg-gray-50/50' : '' }}">
                            @if($cellText !== '-')
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded {{ $cellBg }} text-[10px]" title="{{ $att && $att->time_in ? 'Masuk: ' . substr($att->time_in, 0, 5) : '' }}">{{ $cellText }}</span>
                            @else
                            <span class="{{ $cellBg }}">-</span>
                            @endif
                        </td>
                        @endfor
                        <td class="px-2 py-2 text-center font-bold text-green-600 whitespace-nowrap">
                            {{ $totals['hadir'] }}
                            @if($totals['tugas_khusus'] > 0)
                            <span class="text-[9px] text-indigo-600 font-semibold block" title="Tugas Khusus">+{{ $totals['tugas_khusus'] }} TK</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 text-center text-yellow-600">{{ $totals['sakit'] }}</td>
                        <td class="px-2 py-2 text-center text-blue-600">{{ $totals['izin'] }}</td>
                        <td class="px-2 py-2 text-center text-red-600 font-semibold">{{ $totals['alpha'] }}</td>
                        <td class="px-2 py-2 text-center font-bold {{ $pct >= 90 ? 'text-green-600' : ($pct >= 75 ? 'text-yellow-600' : 'text-red-600') }}">{{ $pct }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($schoolId)
    <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-12 text-center">
        <p class="text-gray-600 font-medium">Tidak ada data pegawai untuk unit ini.</p>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-12 text-center">
        <i class="fas fa-school text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-600 font-medium text-lg">Pilih unit sekolah untuk melihat rekapitulasi</p>
    </div>
    @endif
</div>
@endsection
