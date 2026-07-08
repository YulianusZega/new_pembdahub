@extends('layouts.guru')
@section('title', 'Ujian Tugas Akhir - Portal Guru')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    @php
        $teacherModel = \App\Models\Teacher::with('school')->where('user_id', auth()->id())->first();
        $pageTitle = 'Ujian & Sidang Tugas Akhir';
        $entityName = 'Tugas Akhir';
        if ($teacherModel && $teacherModel->school->type === 'SMA') {
            $pageTitle = 'Ujian Penelitian Ilmiah';
            $entityName = 'Penelitian Ilmiah';
        } elseif ($teacherModel && $teacherModel->school->type === 'SMK') {
            $pageTitle = 'Ujian Project Akhir';
            $entityName = 'Project Akhir';
        }
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-3xl shadow-md border border-gray-250 px-6 py-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-850 flex items-center justify-center text-lg border border-purple-305 shadow-sm">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-gray-900 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-xs text-gray-700 mt-0.5 font-medium">Daftar siswa yang harus Anda uji, masukkan nilai akhir sidang, dan berikan catatan evaluasi/kelulusan.</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-3xl shadow-md border border-gray-250 p-5">
        <form action="{{ route('guru.final-projects.ujian.index') }}" method="GET" class="relative">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-550 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau judul {{ $entityName }}..." class="w-full bg-white border border-gray-300 rounded-2xl pl-11 pr-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400 transition font-medium">
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-md border border-gray-250 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-300 text-xs font-black text-gray-700 uppercase tracking-wider text-left">
                    <tr>
                        <th class="py-4 pl-6">Kelompok / Siswa</th>
                        <th class="py-4">Judul {{ $entityName }}</th>
                        <th class="py-4">Jadwal Ujian</th>
                        <th class="py-4 text-center">Nilai Ujian</th>
                        <th class="py-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-250 text-xs text-gray-750">
                    @forelse($examProjects as $p)
                        <tr class="hover:bg-gray-100 transition">
                            <td class="py-4.5 pl-6">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-100 to-indigo-100 text-purple-800 flex items-center justify-center flex-shrink-0 text-sm border border-purple-300">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 text-xs leading-tight truncate">Ketua: {{ $p->student->full_name }}</p>
                                        @if($p->members && $p->members->count() > 1)
                                            <ul class="text-xs text-gray-600 mt-1 space-y-0.5 border-l-2 border-purple-500 pl-1.5 leading-normal font-bold">
                                                @foreach($p->members->where('role', 'member') as $member)
                                                    <li class="truncate">- {{ $member->student->full_name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <p class="text-xs font-black text-purple-605 mt-1.5 uppercase tracking-wider">{{ $p->student->school->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4.5 max-w-[280px]">
                                <p class="font-bold text-gray-900 leading-relaxed truncate" title="{{ $p->title }}">{{ $p->title }}</p>
                                <p class="text-xs text-gray-655 mt-1 font-bold">Pembimbing: {{ $p->advisor->full_name ?? '-' }}</p>
                            </td>
                            <td class="py-4.5">
                                @if($p->exam_date)
                                    <p class="font-bold text-gray-900 flex items-center gap-1"><i class="fas fa-calendar-alt text-purple-650 text-xs"></i>{{ $p->exam_date->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5 flex items-center gap-1 font-bold"><i class="fas fa-clock text-gray-500"></i>{{ $p->exam_date->format('H:i') }} | R: {{ $p->exam_location }}</p>
                                @else
                                    <span class="text-gray-600 font-bold italic">Belum dijadwalkan admin</span>
                                @endif
                            </td>
                            <td class="py-4.5 text-center">
                                @if($p->grade !== null)
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        {{ $p->grade }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-300">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 pr-6 text-right">
                                @if($p->status === 'ready_for_exam')
                                    <button onclick="openGradeModal({{ $p->id }}, '{{ addslashes($p->student->full_name) }}', '{{ addslashes($p->title) }}')" class="inline-flex items-center gap-1 bg-purple-100 hover:bg-purple-200 text-purple-800 font-black px-3 py-1.5 rounded-xl text-xs border border-purple-300 transition-all shadow-sm">
                                        <i class="fas fa-edit"></i> Input Nilai
                                    </button>
                                @else
                                    <button onclick="openGradeModal({{ $p->id }}, '{{ addslashes($p->student->full_name) }}', '{{ addslashes($p->title) }}', {{ $p->grade }}, '{{ addslashes($p->grade_notes) }}')" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-805 font-black px-3 py-1.5 rounded-xl text-xs border border-gray-300 transition-all shadow-sm">
                                        <i class="fas fa-eye"></i> Detail Nilai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-gray-700 italic font-bold">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3.5 border border-gray-300">
                                    <i class="fas fa-graduation-cap text-xl text-gray-500"></i>
                                </div>
                                <p class="text-xs font-semibold text-gray-600">Belum ada jadwal menguji {{ $entityName }} bagi Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($examProjects->hasPages())
            <div class="px-6 py-4 border-t border-gray-255 bg-gray-50">
                {{ $examProjects->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Grade Modal --}}
<div id="grade-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-black/60" onclick="closeGradeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-250">
            <div class="bg-white px-6 py-5 border-b border-gray-200 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center text-purple-850 text-sm border border-purple-250">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="text-base font-extrabold text-gray-900">
                    Evaluasi & Penilaian Sidang
                </h3>
            </div>
            <form id="grade-form" action="" method="POST">
                @csrf
                <div class="px-6 py-5 space-y-4 text-xs">
                    <div>
                        <span class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Siswa Ujian</span>
                        <p id="modal-student-name" class="font-extrabold text-gray-950 text-sm"></p>
                    </div>
                    <div>
                        <span class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Judul Laporan</span>
                        <p id="modal-project-title" class="font-medium text-gray-905 leading-relaxed text-justify"></p>
                    </div>
                    <div id="modal-input-fields" class="space-y-4 pt-2 border-t border-gray-200">
                        <div>
                            <label for="grade" class="block text-xs font-black text-gray-850 uppercase mb-1.5 tracking-wider">Skor Nilai Sidang (0 - 100)</label>
                            <input type="number" name="grade" id="grade" required min="0" max="100" step="0.1" placeholder="Masukkan nilai kuantitatif..." class="w-full border border-gray-350 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
                        </div>
                        <div>
                            <label for="grade_notes" class="block text-xs font-black text-gray-855 uppercase mb-1.5 tracking-wider">Catatan / Rekomendasi Kelulusan</label>
                            <textarea name="grade_notes" id="grade_notes" rows="4" placeholder="Berikan catatan perbaikan laporan, kelemahan/kelebihan proyek, dan rekomendasi hasil kelulusan..." class="w-full border border-gray-355 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400 transition leading-relaxed"></textarea>
                        </div>
                    </div>
                    <div id="modal-display-fields" class="space-y-3 hidden bg-gray-100 border border-gray-300 rounded-2xl p-4">
                        <div>
                            <span class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-0.5">Nilai Akhir Sidang</span>
                            <p id="display-grade" class="font-black text-gray-850 text-sm bg-emerald-100 text-emerald-800 px-3 py-1 rounded-xl w-fit border border-emerald-300 shadow-sm"></p>
                        </div>
                        <div class="pt-2 border-t border-gray-300 mt-2">
                            <span class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Catatan Dewan Penguji</span>
                            <p id="display-notes" class="font-bold text-gray-900 italic text-justify leading-relaxed whitespace-pre-line"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-2 border-t border-gray-200 rounded-b-3xl">
                    <button type="button" onclick="closeGradeModal()" class="bg-white hover:bg-gray-50 text-gray-700 font-extrabold px-5 py-2.5 rounded-xl text-xs border border-gray-300 transition-all">
                        Tutup
                    </button>
                    <button type="submit" id="submit-grade-btn" class="bg-purple-600 hover:bg-purple-700 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-md transition-all">
                        Simpan & Selesaikan Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openGradeModal(id, studentName, title, grade = null, notes = '') {
        const modal = document.getElementById('grade-modal');
        const form = document.getElementById('grade-form');
        const studentText = document.getElementById('modal-student-name');
        const titleText = document.getElementById('modal-project-title');
        
        studentText.innerText = studentName;
        titleText.innerText = title;
        
        const inputs = document.getElementById('modal-input-fields');
        const displays = document.getElementById('modal-display-fields');
        const submitBtn = document.getElementById('submit-grade-btn');

        if (grade !== null) {
            // Read-only view
            inputs.classList.add('hidden');
            displays.classList.remove('hidden');
            document.getElementById('display-grade').innerText = grade + " / 100";
            document.getElementById('display-notes').innerText = notes || "Tidak ada catatan evaluasi.";
            submitBtn.classList.add('hidden');
        } else {
            // Form input view
            inputs.classList.remove('hidden');
            displays.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            
            // Set form action dynamically
            form.action = "{{ url('/guru/final-projects/ujian') }}/" + id + "/grade";
            document.getElementById('grade').value = '';
            document.getElementById('grade_notes').value = '';
        }
        
        modal.classList.remove('hidden');
    }

    function closeGradeModal() {
        document.getElementById('grade-modal').classList.add('hidden');
    }
</script>
@endsection
