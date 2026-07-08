@extends('layouts.admin')

@section('title', 'Daftar Nilai - Admin')

@section('content')
<div class="space-y-6" x-data="{ expandedRow: null }">
    <!-- Modern Header -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Nilai Siswa</h1>
                <p class="text-gray-600 hover:text-purple-600 transition tracking-tight">Pantau dan kelola akumulasi nilai akademik dari berbagai sumber</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if(count($filters) > 0)
                <a href="{{ route('admin.grades.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-undo text-xs"></i> Reset Filter
                </a>
            @endif
        </div>
    </div>

    <!-- 🔍 Filter Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <i class="fas fa-filter text-8xl transform rotate-12"></i>
        </div>
        <form action="{{ route('admin.grades.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 relative z-10">
            <!-- Academic Year -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">Tahun Pelajaran</label>
                <select name="academic_year_id" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua TP</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>{{ $ay->year }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Semester -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">Semester</label>
                <select name="semester_id" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ ($filters['semester_id'] ?? '') == $sem->id ? 'selected' : '' }}>{{ $sem->semester_name }} ({{ $sem->academicYear->year }})</option>
                    @endforeach
                </select>
            </div>
            <!-- Unit Sekolah -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">Unit Sekolah</label>
                <select name="school_id" onchange="this.form.submit()" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Unit</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}" {{ ($filters['school_id'] ?? '') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Kelas -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">Kelas</label>
                <select name="classroom_id" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ ($filters['classroom_id'] ?? '') == $cls->id ? 'selected' : '' }}>{{ $cls->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-violet-600 text-white px-8 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-search text-sm"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <span class="text-green-700 font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-gradient-to-r from-purple-600 to-violet-700 text-white border-b border-purple-800">
                        <th class="p-4 text-center w-12"><i class="fas fa-hashtag text-[10px] opacity-60"></i></th>
                        <th class="p-4 text-left font-bold text-xs uppercase tracking-wider">Identitas Siswa</th>
                        <th class="p-4 text-left font-bold text-xs uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="p-4 text-center font-bold text-xs uppercase tracking-wider">Tugas</th>
                        <th class="p-4 text-center font-bold text-xs uppercase tracking-wider">UTS</th>
                        <th class="p-4 text-center font-bold text-xs uppercase tracking-wider">UAS</th>
                        <th class="p-4 text-center font-bold text-xs uppercase tracking-wider">Sikap</th>
                        <th class="p-4 text-center font-bold text-xs uppercase tracking-wider">Hasil Akhir</th>
                        <th class="p-4 text-center w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                    $grouped = $grades->groupBy(function($g) {
                        return $g->student_id.'-'.$g->subject_id.'-'.$g->semester_id;
                    });

                    $getSourceInfo = function($grade) {
                        if (!$grade) return null;
                        $type = $grade->lms_source_type;
                        $notes = $grade->notes ?: 'Input Manual';
                        
                        $icon = match($type) {
                            'quiz_attempt' => 'fa-laptop-code text-indigo-500',
                            'submission'   => 'fa-book text-blue-500',
                            'cbt_exam'     => 'fa-desktop text-orange-500',
                            default        => 'fa-keyboard text-gray-400'
                        };
                        
                        $label = match($type) {
                            'quiz_attempt' => 'LMS Kuis',
                            'submission'   => 'LMS Tugas',
                            'cbt_exam'     => 'CBT Exam',
                            default        => 'Manual'
                        };

                        return (object)['icon' => $icon, 'label' => $label, 'notes' => $notes, 'score' => $grade->score];
                    };
                    @endphp

                    @php $no = ($grades->currentPage() - 1) * $grades->perPage() + 1; @endphp
                    @foreach($grouped as $groupId => $rows)
                    @php
                        $g = $rows->first();
                        $rowTugas = $rows->where('grade_type','tugas');
                        $rowUts   = $rows->where('grade_type','uts');
                        $rowUas   = $rows->where('grade_type','uas');
                        $rowSikap = $rows->where('grade_type','sikap');

                        $tugasVal = $rowTugas->avg('score');
                        $utsVal   = $rowUts->avg('score');
                        $uasVal   = $rowUas->avg('score');
                        $sikapVal = $rowSikap->avg('score');

                        $avg = collect([$tugasVal, $utsVal, $uasVal, $sikapVal])->filter(fn($v) => $v !== null)->avg();
                    @endphp
                    <tr class="hover:bg-purple-50/30 transition duration-150 group" :class="expandedRow === '{{ $groupId }}' ? 'bg-purple-50/50' : ''">
                        <td class="p-4 text-center font-mono text-[10px] text-gray-400">{{ $no++ }}</td>
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800">{{ $g->student->full_name ?? '-' }}</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-mono uppercase tracking-tighter">{{ $g->student->nisn ?? '-' }}</span>
                                    <span class="text-[9px] font-bold text-purple-400">{{ $g->classroom->class_name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-700">{{ $g->subject->subject_name ?? '-' }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <i class="fas fa-chalkboard-teacher text-[10px] text-gray-300"></i>
                                    <span class="text-[10px] text-gray-500">{{ $g->teacher->full_name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            @if($tugasVal !== null)
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100 shadow-sm">{{ number_format($tugasVal, 0) }}</span>
                            @else
                                <span class="text-gray-200 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($utsVal !== null)
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100 shadow-sm">{{ number_format($utsVal, 0) }}</span>
                            @else
                                <span class="text-gray-200 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($uasVal !== null)
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg text-xs font-bold border border-rose-100 shadow-sm">{{ number_format($uasVal, 0) }}</span>
                            @else
                                <span class="text-gray-200 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($sikapVal !== null)
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100 shadow-sm">{{ number_format($sikapVal, 0) }}</span>
                            @else
                                <span class="text-gray-200 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($avg !== null)
                                <div class="flex flex-col items-center">
                                    <span class="px-3 py-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-bold shadow-md">{{ number_format($avg, 1) }}</span>
                                    <span class="text-[9px] font-bold mt-1 tracking-tighter {{ $avg >= 75 ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $avg >= 75 ? 'LULUS' : 'TIDAK LULUS' }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-200 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button @click="expandedRow = (expandedRow === '{{ $groupId }}' ? null : '{{ $groupId }}')" 
                                    class="w-8 h-8 rounded-full hover:bg-white flex items-center justify-center transition shadow-sm border border-transparent hover:border-gray-100 group-hover:bg-purple-100/50">
                                <i class="fas fa-chevron-right text-xs transition-transform duration-300" 
                                   :class="expandedRow === '{{ $groupId }}' ? 'rotate-90 text-purple-600' : 'text-gray-300'"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- 🔓 Expanded Row for Details -->
                    <tr x-show="expandedRow === '{{ $groupId }}'" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="bg-gray-50/80 border-b border-gray-100 shadow-inner">
                        <td colspan="9" class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Group Details -->
                                @foreach(['tugas' => 'Tugas/Harian', 'uts' => 'PTS (Tengah Semester)', 'uas' => 'PAS (Akhir Semester)', 'sikap' => 'Nilai Sikap'] as $type => $label)
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                                            {{ $label }}
                                            <span class="w-2 h-2 rounded-full {{ $type == 'tugas' ? 'bg-blue-400' : ($type == 'uts' ? 'bg-green-400' : ($type == 'uas' ? 'bg-rose-400' : 'bg-amber-400')) }}"></span>
                                        </h4>
                                        <div class="space-y-2">
                                            @php $typeRows = $rows->where('grade_type', $type); @endphp
                                            @forelse($typeRows as $row)
                                                @php $info = $getSourceInfo($row); @endphp
                                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:border-purple-200 border border-transparent transition group/item">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-xs border border-gray-50 group-hover/item:shadow-sm">
                                                            <i class="fas {{ $info->icon }} text-xs"></i>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-[11px] font-bold text-gray-700 leading-tight">{{ $info->label }}</span>
                                                            <span class="text-[9px] text-gray-500 line-clamp-1 max-w-[150px]">{{ $info->notes }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-bold text-gray-800">{{ $info->score }}</span>
                                                </div>
                                            @empty
                                                <div class="text-center py-4 opacity-30 grayscale">
                                                    <i class="fas fa-folder-open text-2xl"></i>
                                                    <p class="text-[8px] mt-1 font-bold">BELUM ADA DATA</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">{{ $grades->appends(request()->query())->links() }}</div>
</div>
@endsection