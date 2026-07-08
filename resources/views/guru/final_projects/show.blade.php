@extends('layouts.guru')
@section('title', 'Detail Bimbingan Tugas Akhir - Portal Guru')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    @php
        $teacherModel = \App\Models\Teacher::with('school')->where('user_id', auth()->id())->first();
        $pageTitle = 'Detail Bimbingan Tugas Akhir';
        $entityName = 'Tugas Akhir';
        if ($teacherModel && $teacherModel->school->type === 'SMA') {
            $pageTitle = 'Detail Bimbingan Penelitian';
            $entityName = 'Penelitian Ilmiah';
        } elseif ($teacherModel && $teacherModel->school->type === 'SMK') {
            $pageTitle = 'Detail Bimbingan Project Akhir';
            $entityName = 'Project Akhir';
        }
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-3xl shadow-md border border-gray-250 px-6 py-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.final-projects.bimbingan.index') }}" class="w-9 h-9 rounded-xl bg-white hover:bg-gray-100 flex items-center justify-center text-gray-700 transition-all border border-gray-300 shadow-sm">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-gray-900 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-xs text-gray-700 mt-0.5 font-medium">Pantau kemajuan laporan dan verifikasi jurnal progress logbook siswa.</p>
            </div>
        </div>
        
        @if(in_array($project->status, ['approved', 'in_progress']))
            <form action="{{ route('guru.final-projects.bimbingan.ready', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyatakan laporan {{ $entityName }} siswa ini sudah layak diujikan di depan dewan penguji?')">
                @csrf
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-extrabold px-5 py-3 rounded-xl text-xs shadow-md hover:shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                    <i class="fas fa-graduation-cap"></i> Nyatakan Layak Sidang
                </button>
            </form>
        @endif
    </div>

            @php
                $stageKeys = ['bab1', 'bab2', 'bab3', 'bab4', 'bab5', 'sidang', 'completed'];
                $currentIndex = array_search($project->current_stage, $stageKeys);
                $stages = \App\Models\FinalProject::getStages();
            @endphp
            
            {{-- Visual Stage Progress Tracker --}}
            <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-6">
                <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                    <i class="fas fa-route text-emerald-600"></i> Alur Tahapan &amp; Progress Penulisan Kelompok
                </h3>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-2 relative pt-2">
                    @foreach($stages as $key => $stg)
                        @php
                            $stgIndex = array_search($key, $stageKeys);
                            $isCompleted = $stgIndex < $currentIndex;
                            $isActive = $stgIndex === $currentIndex;
                            $isLocked = $stgIndex > $currentIndex;
                        @endphp
                        <div class="flex-1 w-full relative z-10">
                            <div class="flex md:flex-col items-center gap-3 md:gap-2 text-center">
                                {{-- Circle icon indicator --}}
                                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $isCompleted ? 'bg-emerald-500 border-emerald-600 text-white shadow-sm' : ($isActive ? 'bg-amber-500 border-amber-600 text-white shadow-md animate-pulse' : 'bg-gray-100 border-gray-300 text-gray-400') }}">
                                    @if($isCompleted)
                                        <i class="fas fa-check text-xs"></i>
                                    @elseif($isActive)
                                        <i class="fas fa-spinner fa-spin text-xs"></i>
                                    @else
                                        <i class="fas fa-lock text-xs"></i>
                                    @endif
                                </div>
                                
                                {{-- Text label --}}
                                <div class="text-left md:text-center min-w-0">
                                    <p class="text-xs font-black {{ $isActive ? 'text-amber-700' : ($isCompleted ? 'text-emerald-700' : 'text-gray-500') }}">{{ $stg['name'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold hidden md:block px-2 line-clamp-2 mt-0.5 leading-tight">{{ $stg['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

    {{-- Detail Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Student & Project details card --}}
        <div class="space-y-6 lg:col-span-1">
            {{-- Student Info --}}
            <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider border-b border-gray-200 pb-2">Identitas Kelompok / Siswa</h3>
                <div class="flex items-start gap-3.5 pb-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-300 shadow-sm">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-extrabold text-gray-900 text-xs leading-tight">Ketua: {{ $project->student->full_name }}</p>
                        @if($project->members && $project->members->count() > 1)
                            <ul class="text-xs text-gray-600 font-bold mt-1 space-y-0.5 border-l-2 border-emerald-500 pl-1.5 leading-normal">
                                @foreach($project->members->where('role', 'member') as $member)
                                    <li class="truncate">- {{ $member->student->full_name }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <p class="text-xs font-black text-emerald-600 mt-2 uppercase tracking-wider">{{ $project->student->school->name }}</p>
                    </div>
                </div>
                <div class="space-y-2.5 text-xs border-t border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold">NISN:</span>
                        <span class="font-extrabold text-gray-850 bg-gray-100 px-2 py-0.5 rounded border border-gray-300">{{ $project->student->nisn }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold">NIS:</span>
                        <span class="font-extrabold text-gray-850">{{ $project->student->nis }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-bold">Email:</span>
                        <span class="font-extrabold text-gray-900 truncate max-w-[150px]" title="{{ $project->student->user->email }}">{{ $project->student->user->email }}</span>
                    </div>
                </div>
            </div>

            {{-- Project Title & Abstract --}}
            <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider border-b border-gray-200 pb-2">Topik & Abstrak</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs text-gray-750 font-extrabold uppercase mb-1.5 tracking-wider">Judul Penelitian</span>
                        <p class="text-xs font-extrabold text-gray-900 leading-relaxed">{{ $project->title }}</p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-750 font-extrabold uppercase mb-1.5 tracking-wider">Abstrak / Rencana Proyek</span>
                        <p class="text-xs text-gray-850 font-medium leading-relaxed text-justify whitespace-pre-line">{{ $project->abstract }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logbook list & Review Forms --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-255 shadow-md p-6 space-y-5">
            <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                <i class="fas fa-history text-emerald-600"></i> Logbook Progress & Bimbingan
            </h3>
            
            <div class="relative border-l-2 border-emerald-500 pl-8 ml-4 space-y-6 pt-2">
                @forelse($project->logs as $l)
                    <div class="relative">
                        {{-- Timeline bullet --}}
                        <div class="absolute top-1 w-4 h-4 rounded-full bg-white border-4 border-emerald-600 shadow-sm flex items-center justify-center" style="left: -40px;">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-600"></div>
                        </div>
                        <div class="space-y-3.5 pl-5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs text-gray-700 font-extrabold uppercase tracking-wider"><i class="fas fa-calendar mr-1.5 text-gray-600"></i>{{ $l->log_date->translatedFormat('d M Y') }}</p>
                                    <span class="bg-amber-100 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-lg font-black text-[10px] uppercase tracking-wider">{{ $stages[$l->stage]['name'] ?? 'Bab I' }}</span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black border {{ $l->status === 'approved' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($l->status === 'rejected' ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-gray-150 text-gray-800 border-gray-300') }}">
                                    @if($l->status === 'approved')
                                        <i class="fas fa-check-circle mr-1"></i> ACC / Disetujui
                                    @elseif($l->status === 'rejected')
                                        <i class="fas fa-times-circle mr-1"></i> Perlu Revisi
                                    @else
                                        <i class="fas fa-clock mr-1"></i> Menunggu Review
                                    @endif
                                </span>
                            </div>
                            
                            <div class="bg-gray-50 border border-gray-300 rounded-2xl px-4 py-3.5 text-xs leading-relaxed text-gray-900 space-y-2 shadow-sm">
                                <p class="font-bold whitespace-pre-line">{{ $l->activity }}</p>
                                @if($l->documentation_file)
                                    <div class="pt-2.5 border-t border-gray-200 mt-2.5 flex items-center gap-3 flex-wrap">
                                        <button type="button" @click="$dispatch('open-preview-modal', { url: '{{ asset('storage/' . $l->documentation_file) }}', name: 'Lampiran Tahap {{ $stages[$l->stage]['name'] ?? 'Logbook' }} ({{ $project->student->full_name }})' })" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-extrabold transition shadow-md hover:shadow-lg transform active:scale-95">
                                            <i class="fas fa-eye mr-2 text-emerald-100"></i> Baca / Lihat Dokumen Langsung di Layar
                                        </button>
                                        <a href="{{ asset('storage/' . $l->documentation_file) }}" target="_blank" download class="inline-flex items-center px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-[11px] text-gray-700 font-bold transition border border-gray-300">
                                            <i class="fas fa-download mr-1.5 text-gray-500"></i> Unduh File Asli
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if($l->status === 'submitted')
                                {{-- Feedback Form --}}
                                <div class="bg-gray-100 border border-gray-300 rounded-2xl p-4 ml-4 relative shadow-sm">
                                    <div class="absolute -top-2 left-4 w-3 h-3 bg-gray-100 border-t border-l border-gray-300 transform rotate-45"></div>
                                    <form action="{{ route('guru.final-projects.bimbingan.review-log', [$project->id, $l->id]) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-black text-gray-800 uppercase mb-1.5 tracking-wider">Keputusan Review</label>
                                            <select name="status" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-xs text-gray-850 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
                                                <option value="approved" class="text-emerald-700 font-bold">✓ Setujui &amp; Lanjutkan Bab berikutnya (ACC)</option>
                                                <option value="rejected" class="text-rose-700 font-bold">⚠ Tolak &amp; Minta Siswa Lakukan Revisi</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-gray-800 uppercase mb-1.5 tracking-wider">Catatan Bimbingan / Revisi Pembimbing</label>
                                            <textarea name="advisor_feedback" rows="2" required placeholder="Tuliskan revisi, masukan, atau saran penyempurnaan..." class="w-full bg-white border border-gray-350 rounded-xl px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 transition leading-relaxed"></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-md transition transform active:scale-95 flex items-center gap-1.5">
                                                <i class="fas fa-check-circle"></i> Simpan Catatan Review
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                {{-- Feedback Displayed --}}
                                <div class="bg-indigo-50 border border-indigo-305 rounded-2xl p-4 text-xs text-indigo-950 ml-4 relative shadow-sm">
                                    <div class="absolute -top-2 left-4 w-3 h-3 bg-indigo-50 border-t border-l border-indigo-305 transform rotate-45"></div>
                                    <p class="font-extrabold text-xs text-indigo-900 mb-1 flex items-center gap-1.5"><i class="fas fa-comment-dots"></i> Catatan/Tanggapan Pembimbing:</p>
                                    <p class="italic font-bold">"{{ $l->advisor_feedback }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-700 italic font-bold">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-300 shadow-sm">
                            <i class="fas fa-folder-open text-base text-gray-500"></i>
                        </div>
                        <p class="text-xs">Siswa belum pernah menginput catatan logbook progress bimbingan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('components.preview-modal')
@endsection
