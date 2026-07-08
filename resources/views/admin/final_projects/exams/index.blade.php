@extends('layouts.admin')
@php
    $user = auth()->user();
    $schoolType = $user->school ? strtoupper($user->school->type) : 'ALL';
    $isSMA = $schoolType === 'SMA';
    $isSMK = $schoolType === 'SMK';
    
    $pageTitle = 'Jadwal & Ujian ';
    $entityName = 'Tugas Akhir';
    if ($isSMA) {
        $pageTitle .= 'Penelitian Ilmiah';
        $entityName = 'Penelitian Ilmiah';
    } else if ($isSMK) {
        $pageTitle .= 'Project Akhir';
        $entityName = 'Project Akhir';
    } else {
        $pageTitle .= 'Penelitian/Project';
        $entityName = 'Penelitian / Project Akhir';
    }
@endphp
@section('title', $pageTitle . ' - Portal Admin')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-calendar-check text-purple-500"></i> {{ $pageTitle }}
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola jadwal ujian/sidang akhir siswa kelas XII serta penugasan guru penguji.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-circle-exclamation text-rose-500"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <i class="fas fa-circle-exclamation text-rose-500"></i> Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.final-projects.exams.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau judul..." class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
            </div>

            {{-- School filter (Superadmin only) --}}
            @if($isSA)
                <div>
                    <select name="school_id" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
                        <option value="">Semua Sekolah...</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Actions --}}
            <div class="{{ $isSA ? 'md:col-span-2' : 'md:col-span-3' }} flex justify-end gap-2">
                <button type="submit" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-left">
                    <tr>
                        <th class="py-3.5 pl-5">Kelompok / Siswa</th>
                        <th class="py-3.5">Judul {{ $entityName }}</th>
                        <th class="py-3.5">Detail Sidang</th>
                        <th class="py-3.5 text-center">Nilai Ujian</th>
                        <th class="py-3.5 pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($projects as $p)
                        <tr class="hover:bg-gray-50/50 transition align-top">
                            <td class="py-4 pl-5">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shadow-sm flex-shrink-0 flex items-center justify-center text-purple-600">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-800 text-sm leading-tight truncate">Ketua: {{ $p->student->full_name }}{{ $p->members->count() > 1 ? ' .dkk' : '' }}</p>
                                        @if($p->members && $p->members->count() > 1)
                                            <ul class="text-[10px] text-gray-500 mt-1 space-y-0.5 border-l-2 border-purple-100 pl-1.5">
                                                @foreach($p->members->where('role', 'member') as $member)
                                                    <li class="truncate">- {{ $member->student->full_name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <p class="text-[9px] font-bold text-purple-400 mt-1 uppercase">{{ $p->student->school->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 max-w-[280px]">
                                <p class="font-semibold text-gray-850 leading-normal" title="{{ $p->title }}">{{ $p->title }}</p>
                                <p class="text-[10px] text-gray-450 mt-1">Pembimbing: {{ $p->advisor->full_name ?? '-' }}</p>
                            </td>
                            <td class="py-4">
                                @if($p->exam_date)
                                    <p class="font-bold text-gray-750 flex items-center gap-1"><i class="fas fa-calendar-alt text-purple-400"></i> {{ $p->exam_date->translatedFormat('d M Y, H:i') }} WIB</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1"><i class="fas fa-location-dot"></i> R: {{ $p->exam_location }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1"><i class="fas fa-user-tie"></i> Penguji 1: {{ $p->examiner->full_name ?? '-' }}</p>
                                    @if($p->examiner2_id)
                                        <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1"><i class="fas fa-user-tie"></i> Penguji 2: {{ $p->examiner2->full_name ?? '-' }}</p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Jadwal
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-center">
                                @if($p->grade !== null)
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $p->grade }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">Belum diuji</span>
                                @endif
                            </td>
                            <td class="py-4 pr-5 text-right">
                                @if($p->status === 'ready_for_exam')
                                    <button onclick="openScheduleModal(this)" 
                                            data-id="{{ $p->id }}" 
                                            data-student="{{ $p->student->full_name }}{{ $p->members->count() > 1 ? ' .dkk' : '' }}" 
                                            data-title="{{ $p->title }}" 
                                            data-advisor-id="{{ (int)($p->advisor_id ?? 0) }}" 
                                            data-school-id="{{ (int)($p->student->school_id ?? 0) }}"
                                            data-examiner-id="{{ (int)($p->examiner_id ?? 0) }}"
                                            data-examiner2-id="{{ (int)($p->examiner2_id ?? 0) }}"
                                            data-location="{{ $p->exam_location }}"
                                            data-date="{{ $p->exam_date ? $p->exam_date->format('Y-m-d\TH:i') : '' }}"
                                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs shadow-sm transition">
                                        {{ $p->exam_date ? 'Reschedule' : 'Jadwalkan Sidang' }}
                                    </button>
                                @else
                                    <button disabled class="bg-gray-50 text-gray-400 font-bold px-3 py-1.5 rounded-lg text-xs border border-gray-150 cursor-not-allowed">
                                        Ujian Selesai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 italic">
                                Belum ada siswa yang dinyatakan Layak Sidang oleh guru pembimbing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Schedule Modal --}}
<div id="schedule-modal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm z-0" onclick="closeScheduleModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        {{-- Modal Content --}}
        <div class="relative z-10 inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl border-2 border-gray-300 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            {{-- Header --}}
            <div class="bg-white px-6 py-5 border-b-2 border-gray-250 flex items-center justify-between">
                <h3 class="text-base font-black text-gray-950 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-md border border-purple-500">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </div>
                    Jadwalkan Sidang Ujian
                </h3>
                <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-800 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Form --}}
            <form id="schedule-form" action="" method="POST">
                @csrf
                <div class="px-6 py-5 space-y-5 text-xs">
                    {{-- Info Box --}}
                    <div class="bg-purple-50 rounded-2xl p-4.5 space-y-2.5 border-2 border-purple-200">
                        <div>
                            <span class="text-[10px] text-purple-950 font-black uppercase tracking-wider">Siswa Peserta</span>
                            <p id="modal-student-name" class="font-black text-gray-950 text-sm mt-0.5"></p>
                        </div>
                        <div class="pt-2 border-t border-purple-200">
                            <span class="text-[10px] text-purple-950 font-black uppercase tracking-wider">Judul Laporan</span>
                            <p id="modal-project-title" class="font-bold text-gray-900 text-xs leading-relaxed text-justify mt-0.5"></p>
                        </div>
                    </div>

                    {{-- Tanggal & Waktu --}}
                    <div>
                        <label for="exam_date" class="block text-[10px] font-black text-gray-950 uppercase mb-1.5 tracking-wider">Tanggal & Waktu Ujian</label>
                        <input type="datetime-local" name="exam_date" id="exam_date" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 font-bold text-gray-950 focus:outline-none focus:ring-2 focus:ring-purple-400 transition bg-white">
                    </div>

                    {{-- Ruang Sidang --}}
                    <div>
                        <label for="exam_location" class="block text-[10px] font-black text-gray-950 uppercase mb-1.5 tracking-wider">Ruang Sidang / Lokasi</label>
                        <input type="text" name="exam_location" id="exam_location" required placeholder="Contoh: Ruang Rapat Lt. 2 / Google Meet Link..." class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 font-bold text-gray-950 focus:outline-none focus:ring-2 focus:ring-purple-400 transition bg-white">
                    </div>

                    {{-- Guru Penguji 1 --}}
                    <div>
                        <label for="examiner_id" class="block text-[10px] font-black text-gray-950 uppercase mb-1.5 tracking-wider">Guru Penguji 1</label>
                        <select name="examiner_id" id="examiner_id" required class="w-full bg-white border-2 border-gray-300 rounded-xl px-4 py-2.5 font-bold text-gray-950 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
                            <option value="">Pilih Guru Penguji 1...</option>
                        </select>
                    </div>

                    {{-- Guru Penguji 2 --}}
                    <div>
                        <label for="examiner2_id" class="block text-[10px] font-black text-gray-950 uppercase mb-1.5 tracking-wider">Guru Penguji 2 (Opsional)</label>
                        <select name="examiner2_id" id="examiner2_id" class="w-full bg-white border-2 border-gray-300 rounded-xl px-4 py-2.5 font-bold text-gray-950 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
                            <option value="">Pilih Guru Penguji 2 (Belum ada)...</option>
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t-2 border-gray-250 rounded-b-3xl">
                    <button type="button" onclick="closeScheduleModal()" class="bg-white hover:bg-gray-100 text-gray-700 font-extrabold px-5 py-2.5 rounded-xl text-xs border-2 border-gray-300 shadow-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs border-2 border-purple-800 shadow-md transition transform active:scale-95">
                        Terbitkan Jadwal Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const allTeachers = @json($teachers);

    function openScheduleModal(btn) {
        const id = btn.getAttribute('data-id');
        const studentName = btn.getAttribute('data-student');
        const title = btn.getAttribute('data-title');
        const schoolId = parseInt(btn.getAttribute('data-school-id') || 0);
        const advisorId = parseInt(btn.getAttribute('data-advisor-id') || 0);
        const currentExaminerId = parseInt(btn.getAttribute('data-examiner-id') || 0);
        const currentExaminer2Id = parseInt(btn.getAttribute('data-examiner2-id') || 0);
        const currentDate = btn.getAttribute('data-date') || '';
        const currentLocation = btn.getAttribute('data-location') || '';

        const modal = document.getElementById('schedule-modal');
        const form = document.getElementById('schedule-form');
        const studentText = document.getElementById('modal-student-name');
        const titleText = document.getElementById('modal-project-title');
        
        studentText.innerText = studentName;
        titleText.innerText = title;

        // Pre-populate values for rescheduling
        document.getElementById('exam_date').value = currentDate;
        document.getElementById('exam_location').value = currentLocation;
        
        // Membangun opsi Guru Penguji secara dinamis
        const examinerSelect = document.getElementById('examiner_id');
        const examiner2Select = document.getElementById('examiner2_id');
        
        const buildSelect = (selectEl, defaultText, selectedVal) => {
            if (!selectEl) return;
            selectEl.innerHTML = '';
            
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.innerText = defaultText;
            selectEl.appendChild(defaultOpt);
            
            const filtered = allTeachers.filter(t => t.school_id == schoolId && t.id != advisorId);
            filtered.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.innerText = t.full_name + ' (' + (t.school ? t.school.name : 'Sekolah') + ')';
                if (t.id == selectedVal) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });
        };

        buildSelect(examinerSelect, 'Pilih Guru Penguji 1...', currentExaminerId);
        buildSelect(examiner2Select, 'Pilih Guru Penguji 2 (Belum ada)...', currentExaminer2Id);
        
        form.action = "{{ route('admin.final-projects.exams.schedule', ['project' => 'PLACEHOLDER'], false) }}".replace('PLACEHOLDER', id);
        modal.classList.remove('hidden');
    }

    function closeScheduleModal() {
        document.getElementById('schedule-modal').classList.add('hidden');
    }
</script>
@endpush
