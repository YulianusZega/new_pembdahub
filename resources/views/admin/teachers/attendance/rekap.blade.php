@extends('layouts.admin')

@section('title', 'Rekap Absensi Guru')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.teachers.attendance.index', ['school_id' => $schoolId]) }}"
               class="p-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                <i class="fas fa-table text-xl text-white"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Rekap Absensi Guru</h1>
                <p class="text-gray-500 text-sm mt-0.5">
                    {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isKetuaYayasan())
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sekolah</label>
                <select name="school_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="school_id" value="{{ $schoolId }}">
            @endif
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
                <select name="month" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
                <select name="year" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors">
                <i class="fas fa-search mr-1"></i> Tampilkan
            </button>
        </form>
    </div>

    @if($schoolId && $teachers->isNotEmpty())

    {{-- Legend --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-3 text-xs">
            @php
                $legend = [
                    'H'  => ['label' => 'Hadir',      'bg' => 'bg-green-100  text-green-700'],
                    'S'  => ['label' => 'Sakit',       'bg' => 'bg-yellow-100 text-yellow-700'],
                    'I'  => ['label' => 'Izin',        'bg' => 'bg-blue-100   text-blue-700'],
                    'A'  => ['label' => 'Alpha',        'bg' => 'bg-red-100    text-red-700'],
                    'DL' => ['label' => 'Dinas Luar',  'bg' => 'bg-purple-100 text-purple-700'],
                    'C'  => ['label' => 'Cuti',        'bg' => 'bg-indigo-100 text-indigo-700'],
                    '–'  => ['label' => 'Tidak Ada Data', 'bg' => 'bg-gray-100 text-gray-400'],
                ];
            @endphp
            @foreach($legend as $code => $info)
            <span class="flex items-center gap-1.5 {{ $info['bg'] }} px-2.5 py-1 rounded-lg font-semibold">
                {{ $code }} = {{ $info['label'] }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- Rekap Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase sticky left-0 bg-gray-50 z-10 min-w-[160px]">Nama Guru</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dayOfWeek = \Carbon\Carbon::create($year, $month, $d)->dayOfWeek;
                            $isWeekend = in_array($dayOfWeek, [0, 6]);
                        @endphp
                        <th class="px-1.5 py-3 text-center font-bold {{ $isWeekend ? 'text-red-400 bg-red-50' : 'text-gray-500' }} w-8">{{ $d }}</th>
                        @endfor
                        {{-- Summary columns --}}
                        <th class="px-2 py-3 text-center font-bold text-green-600 bg-green-50 w-10">H</th>
                        <th class="px-2 py-3 text-center font-bold text-yellow-600 bg-yellow-50 w-10">S</th>
                        <th class="px-2 py-3 text-center font-bold text-blue-600 bg-blue-50 w-10">I</th>
                        <th class="px-2 py-3 text-center font-bold text-red-600 bg-red-50 w-10">A</th>
                        <th class="px-2 py-3 text-center font-bold text-purple-600 bg-purple-50 w-10">DL</th>
                        <th class="px-2 py-3 text-center font-bold text-indigo-600 bg-indigo-50 w-10">C</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($teachers as $teacher)
                    @php
                        $tData   = $attendanceData[$teacher->id] ?? [];
                        $summary = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0, 'dinas_luar' => 0, 'cuti' => 0];
                        $statusMap = [
                            'hadir'     => ['code' => 'H',  'class' => 'bg-green-100  text-green-700'],
                            'sakit'     => ['code' => 'S',  'class' => 'bg-yellow-100 text-yellow-700'],
                            'izin'      => ['code' => 'I',  'class' => 'bg-blue-100   text-blue-700'],
                            'alpha'     => ['code' => 'A',  'class' => 'bg-red-100    text-red-700'],
                            'dinas_luar'=> ['code' => 'DL', 'class' => 'bg-purple-100 text-purple-700'],
                            'cuti'      => ['code' => 'C',  'class' => 'bg-indigo-100 text-indigo-700'],
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-3 py-2.5 font-semibold text-gray-800 sticky left-0 bg-white hover:bg-gray-50 z-10 border-r border-gray-100">
                            <div>{{ $teacher->full_name }}</div>
                            <div class="text-gray-400 font-normal text-[10px]">{{ $teacher->employee_code ?? '-' }}</div>
                        </td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $att        = $tData[$d] ?? null;
                            $dayOfWeek  = \Carbon\Carbon::create($year, $month, $d)->dayOfWeek;
                            $isWeekend  = in_array($dayOfWeek, [0, 6]);
                            if ($att) $summary[$att->status] = ($summary[$att->status] ?? 0) + 1;
                        @endphp
                        <td class="px-0.5 py-2.5 text-center {{ $isWeekend ? 'bg-red-50/30' : '' }}">
                            @if($att && isset($statusMap[$att->status]))
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded {{ $statusMap[$att->status]['class'] }} font-bold text-[10px]">
                                {{ $statusMap[$att->status]['code'] }}
                            </span>
                            @elseif($isWeekend)
                            <span class="text-red-200 text-[10px]">–</span>
                            @else
                            <span class="text-gray-200">–</span>
                            @endif
                        </td>
                        @endfor
                        {{-- Summary --}}
                        <td class="px-2 py-2.5 text-center font-bold text-green-700 bg-green-50/50">{{ $summary['hadir'] }}</td>
                        <td class="px-2 py-2.5 text-center font-bold text-yellow-700 bg-yellow-50/50">{{ $summary['sakit'] }}</td>
                        <td class="px-2 py-2.5 text-center font-bold text-blue-700 bg-blue-50/50">{{ $summary['izin'] }}</td>
                        <td class="px-2 py-2.5 text-center font-bold text-red-700 bg-red-50/50">{{ $summary['alpha'] }}</td>
                        <td class="px-2 py-2.5 text-center font-bold text-purple-700 bg-purple-50/50">{{ $summary['dinas_luar'] }}</td>
                        <td class="px-2 py-2.5 text-center font-bold text-indigo-700 bg-indigo-50/50">{{ $summary['cuti'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @elseif($schoolId && $teachers->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-slash text-3xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 font-medium">Tidak ada guru aktif di sekolah ini</p>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-20 text-center">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-school text-3xl text-emerald-300"></i>
        </div>
        <p class="text-gray-500 font-medium">Pilih sekolah dan periode untuk melihat rekap</p>
    </div>
    @endif
</div>
@endsection
