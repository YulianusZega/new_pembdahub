@extends('layouts.siswa')
@section('title', $project->type === 'penelitian_ilmiah' ? 'Portal Penelitian Ilmiah' : 'Portal Project Akhir')

@section('content')
<div class="space-y-6">
    {{-- Header Banner based on Project Status --}}
    @php
        $statusColors = match($project->status) {
            'pending' => 'from-amber-600 via-amber-700 to-yellow-700',
            'rejected' => 'from-rose-600 via-rose-700 to-red-700',
            'approved', 'in_progress' => 'from-indigo-700 via-indigo-800 to-violet-900',
            'ready_for_exam' => 'from-cyan-600 via-cyan-700 to-blue-700',
            'completed' => 'from-emerald-600 via-emerald-700 to-teal-700',
            default => 'from-gray-700 to-gray-800'
        };
        $entityName = $project->type === 'penelitian_ilmiah' ? 'Penelitian Ilmiah' : 'Project Akhir';
        $statusLabels = match($project->status) {
            'pending' => 'Menunggu Persetujuan Judul',
            'rejected' => 'Judul ' . $entityName . ' Ditolak',
            'approved' => 'Judul Disetujui (Mulai Bimbingan)',
            'in_progress' => 'Dalam Pengerjaan & Bimbingan',
            'ready_for_exam' => 'Dinyatakan Layak Sidang / Ujian',
            'completed' => 'Lulus & Selesai',
            default => 'Status Tidak Diketahui'
        };
        
        $statusIcons = match($project->status) {
            'pending' => 'fa-clock',
            'rejected' => 'fa-times-circle',
            'approved' => 'fa-check-circle',
            'in_progress' => 'fa-spinner fa-spin',
            'ready_for_exam' => 'fa-graduation-cap',
            'completed' => 'fa-award',
            default => 'fa-info-circle'
        };
    @endphp

    <div class="bg-gradient-to-br {{ $statusColors }} rounded-3xl shadow-xl p-6 md:p-8 text-white relative overflow-hidden border border-white/20">
        {{-- Background decorative grid --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.08)_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <span class="inline-flex items-center gap-3.5 bg-white/30 text-white border border-white/40 pl-6 pr-8 py-2.5 rounded-full text-xs font-extrabold uppercase tracking-wider backdrop-blur-sm">
                    <i class="fas {{ $statusIcons }} text-sm"></i> {{ $statusLabels }}
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold mt-1.5 leading-tight tracking-tight drop-shadow-sm">
                    {{ $project->title }}
                </h1>
                <p class="text-xs text-white font-bold">
                    Jenis: <span class="underline decoration-white/60 decoration-2">{{ $project->type === 'penelitian_ilmiah' ? 'Penelitian Ilmiah (SMA)' : 'Project Akhir (SMK)' }}</span> | Kelas: <span class="bg-white/25 px-2 py-0.5 rounded font-black">{{ $classroom->class_name }}</span>
                </p>
            </div>
            
            @if($project->status === 'rejected')
                <div class="flex-shrink-0">
                    <button onclick="toggleModal('repropose-modal')" class="bg-white hover:bg-gray-55 text-rose-700 font-extrabold px-6 py-3 rounded-2xl text-xs shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-rotate-left"></i> Ajukan Judul Baru
                    </button>
                </div>
            @endif
        </div>
        <div class="absolute right-0 bottom-0 opacity-20 text-[10rem] md:text-[12rem] transform translate-x-12 translate-y-12 pointer-events-none">
            <i class="fas fa-file-invoice"></i>
        </div>
    </div>

    @if($project->status === 'rejected' && $project->rejection_reason)
        <div class="bg-rose-100 border border-rose-350 text-rose-900 px-6 py-4 rounded-2xl text-xs md:text-sm shadow-md flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-200 flex items-center justify-center text-rose-800 flex-shrink-0 mt-0.5 border border-rose-300">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-rose-950 mb-0.5 text-xs md:text-sm">Catatan Penolakan Koordinator/Admin:</h4>
                <p class="italic font-bold">"{{ $project->rejection_reason }}"</p>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-300 flex gap-1 overflow-x-auto">
        <button onclick="switchTab('logbook')" id="tab-btn-logbook" class="tab-btn border-b-2 border-amber-600 text-amber-700 px-5 py-3 font-extrabold text-xs md:text-sm tracking-wide uppercase transition focus:outline-none flex items-center gap-2">
            <i class="fas fa-book-bookmark"></i> Jurnal & Logbook
        </button>
        <button onclick="switchTab('formats')" id="tab-btn-formats" class="tab-btn border-b-2 border-transparent text-gray-600 hover:text-gray-800 px-5 py-3 font-extrabold text-xs md:text-sm tracking-wide uppercase transition focus:outline-none flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> Panduan & Format
        </button>
        <button onclick="switchTab('exam')" id="tab-btn-exam" class="tab-btn border-b-2 border-transparent text-gray-600 hover:text-gray-800 px-5 py-3 font-extrabold text-xs md:text-sm tracking-wide uppercase transition focus:outline-none flex items-center gap-2">
            <i class="fas fa-graduation-cap"></i> Jadwal & Hasil Sidang
        </button>
    </div>

    {{-- Tab Contents --}}
    <div>
        {{-- Tab 1: Logbook --}}
        <div id="tab-content-logbook" class="tab-content space-y-6">
            @php
                $stageKeys = ['bab1', 'bab2', 'bab3', 'bab4', 'bab5', 'sidang', 'completed'];
                $currentIndex = array_search($project->current_stage, $stageKeys);
                $stages = \App\Models\FinalProject::getStages();
                $hasPendingLog = $logs->where('stage', $project->current_stage)->where('status', 'submitted')->isNotEmpty();
            @endphp
            
            {{-- Visual Stage Progress Tracker --}}
            <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-6">
                <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                    <i class="fas fa-route text-amber-600"></i> Alur Tahapan &amp; Progress Penulisan
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Logbook Submission Form (Only if active/approved) --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                        <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                            <i class="fas fa-id-card text-amber-600"></i> Detail Pembimbingan
                        </h3>
                        <div class="space-y-3.5 pt-1 text-xs">
                            <div class="flex justify-between py-2 border-b border-gray-200 items-center">
                                <span class="text-gray-750 font-bold">Pembimbing:</span>
                                <span class="font-extrabold text-gray-800 bg-gray-100 border border-gray-300 px-2.5 py-1 rounded-lg">{{ $project->advisor->full_name ?? 'Belum Ditugaskan' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 items-center">
                                <span class="text-gray-750 font-bold">Tahun Ajaran:</span>
                                <span class="font-extrabold text-gray-850">{{ $project->academicYear->year }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 items-center">
                                <span class="text-gray-750 font-bold">Total Progress:</span>
                                <span class="font-extrabold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-300">{{ $logs->count() }} Kegiatan</span>
                            </div>
                            <div class="pt-3">
                                <span class="text-gray-750 font-bold mb-2 block">Anggota Kelompok:</span>
                                <ul class="space-y-2">
                                    @foreach($project->members as $member)
                                        <li class="flex items-center justify-between bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-amber-800 text-xs font-bold border border-amber-250">
                                                    {{ substr($member->student->full_name, 0, 1) }}
                                                </div>
                                                <span class="font-extrabold text-gray-800 truncate text-[11px]">{{ $member->student->full_name }}</span>
                                            </div>
                                            @if($member->role === 'leader')
                                                <span class="bg-amber-200 text-amber-900 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border border-amber-300">Ketua</span>
                                            @else
                                                <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-gray-300">Anggota</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if(in_array($project->status, ['approved', 'in_progress', 'ready_for_exam']))
                        <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                            <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                                <i class="fas fa-edit text-amber-600"></i> Tambah Log Progress
                            </h3>
                            
                            @if($hasPendingLog)
                                <div class="bg-amber-50 border border-amber-250 rounded-2xl p-4 text-xs text-amber-900 leading-relaxed font-bold space-y-1.5 shadow-sm">
                                    <p class="flex items-center gap-1.5 font-extrabold text-amber-955"><i class="fas fa-exclamation-circle text-amber-600"></i> Menunggu Tinjauan Pembimbing</p>
                                    <p class="font-semibold text-gray-650 leading-relaxed">Anda sudah mengirimkan dokumen untuk bab ini. Harap tunggu hingga guru pembimbing memeriksa dan memberikan feedback/persetujuan (ACC) sebelum mengunggah progress baru.</p>
                                </div>
                            @endif

                            <form action="{{ route('siswa.final-project.log.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Bab / Tahap Bimbingan</label>
                                    <input type="text" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-xs text-gray-850 font-bold focus:outline-none cursor-not-allowed" value="{{ $stages[$project->current_stage]['name'] ?? $project->current_stage }}" readonly>
                                    <input type="hidden" name="stage" value="{{ $project->current_stage }}">
                                </div>
                                <div>
                                    <label for="log_date" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Tanggal Kegiatan</label>
                                    <input type="date" name="log_date" id="log_date" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full border border-gray-350 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition" @if($hasPendingLog) disabled @endif>
                                </div>
                                <div>
                                    <label for="activity" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Rincian Kemajuan Kegiatan / Revisi</label>
                                    <textarea name="activity" id="activity" rows="4" required placeholder="Tuliskan rincian aktivitas bimbingan atau bab penulisan yang Anda selesaikan/konsultasikan..." class="w-full border border-gray-350 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition leading-relaxed" @if($hasPendingLog) disabled @endif></textarea>
                                </div>
                                <div>
                                    <label for="documentation_file" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Dokumen BAB / Pendukung</label>
                                    <input type="file" name="documentation_file" id="documentation_file" class="w-full border border-gray-350 rounded-xl px-4 py-2 text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 transition file:cursor-pointer" @if($hasPendingLog) disabled @endif>
                                    <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-bold">Format: PDF, Word, JPEG, PNG, ZIP. Maks: 5MB</p>
                                </div>
                                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-extrabold py-3 rounded-xl text-xs transition-all shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed" @if($hasPendingLog) disabled @endif>
                                    <i class="fas fa-plus"></i> Simpan Logbook
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-100 border border-gray-300 rounded-3xl p-6 text-center space-y-3 shadow-sm">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mx-auto text-gray-600 border border-gray-300">
                                <i class="fas fa-lock text-base"></i>
                            </div>
                            <p class="text-xs text-gray-800 leading-relaxed font-extrabold">Logbook dikunci hingga judul usulan disetujui admin dan guru pembimbing ditugaskan.</p>
                        </div>
                    @endif
                </div>

                {{-- Logs Timeline --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                    <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                        <i class="fas fa-history text-amber-600"></i> Riwayat Progress Bimbingan
                    </h3>
                    
                    <div class="relative border-l-2 border-amber-500 pl-8 ml-4 space-y-6 pt-2">
                        @forelse($logs as $l)
                            <div class="relative">
                                {{-- Timeline bullet --}}
                                <div class="absolute top-1 w-4 h-4 rounded-full bg-white border-4 border-amber-600 shadow-sm flex items-center justify-center" style="left: -40px;">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-600"></div>
                                </div>
                                <div class="space-y-3 pl-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-xs text-gray-700 font-extrabold uppercase tracking-wider"><i class="fas fa-calendar mr-1.5 text-gray-600"></i>{{ $l->log_date->translatedFormat('d M Y') }}</p>
                                            <span class="bg-amber-100 text-amber-805 border border-amber-200 px-2 py-0.5 rounded-lg font-black text-[10px] uppercase tracking-wider">{{ $stages[$l->stage]['name'] ?? 'Bab I' }}</span>
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
                                    <div class="text-xs text-gray-900 leading-relaxed bg-gray-50 border border-gray-250 rounded-2xl px-4 py-3.5 space-y-2 shadow-sm">
                                        <p class="font-bold whitespace-pre-line">{{ $l->activity }}</p>
                                        @if($l->documentation_file)
                                            <p class="pt-2 border-t border-gray-200 mt-2">
                                                <a href="{{ asset('storage/' . $l->documentation_file) }}" target="_blank" class="inline-flex items-center text-xs text-amber-700 font-extrabold hover:underline">
                                                    <i class="fas fa-paperclip mr-1 text-gray-500"></i> Buka File Dokumentasi
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                    
                                    @if($l->advisor_feedback)
                                        <div class="bg-indigo-50 border border-indigo-305 rounded-2xl p-4 text-xs text-indigo-950 ml-4 relative shadow-sm">
                                            <div class="absolute -top-2 left-4 w-3 h-3 bg-indigo-50 border-t border-l border-indigo-305 transform rotate-45"></div>
                                            <p class="font-extrabold text-xs text-indigo-900 mb-1 flex items-center gap-1.5"><i class="fas fa-comment-dots"></i> Catatan Pembimbing:</p>
                                            <p class="italic font-bold">"{{ $l->advisor_feedback }}"</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-700 italic font-bold">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-300">
                                    <i class="fas fa-folder-open text-base text-gray-500"></i>
                                </div>
                                <p class="text-xs">Belum ada catatan jurnal progress bimbingan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 2: Formats --}}
        <div id="tab-content-formats" class="tab-content hidden bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-6">
            <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                <i class="fas fa-file-pdf text-rose-600"></i> Panduan &amp; Format
            </h2>
            
            {{-- Panduan Resmi Penyusunan Penelitian PDF --}}
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-5 text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-amber-450 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-xl backdrop-blur-sm border border-white/25 shadow-inner">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm md:text-base">Buku Panduan Penyusunan Laporan Penelitian &amp; Project Akhir</h4>
                        <p class="text-xs text-amber-100 font-semibold mt-0.5">Panduan resmi langkah-demi-langkah mengenai sistematika penulisan bab, tata cara bimbingan, dan syarat sidang.</p>
                    </div>
                </div>
                <a href="{{ route('public.guideline.download') }}" class="bg-white hover:bg-amber-50 text-amber-700 px-5 py-2.5 rounded-xl text-xs font-black shadow transition-all hover:shadow-md active:scale-95 flex items-center gap-1.5 flex-shrink-0">
                    <i class="fas fa-download"></i> Unduh Buku Panduan (PDF)
                </a>
            </div>

            <p class="text-xs text-gray-700 leading-relaxed font-bold border-t border-gray-200 pt-4 mt-4">
                Selain buku panduan di atas, silakan unduh file template/format penulisan dokumen laporan resmi dari sekolah berikut:
            </p>

            <div class="space-y-3 pt-1">
                @forelse($formats as $f)
                    <div class="flex items-start gap-3.5 p-4 bg-gray-50 rounded-2xl border border-gray-200 hover:bg-gray-100 hover:border-gray-350 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 flex-shrink-0 text-base border border-rose-300 shadow-sm">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <p class="text-xs font-bold text-gray-950 truncate" title="{{ $f->title }}">{{ $f->title }}</p>
                            @if($f->description)
                                <p class="text-xs text-gray-650 leading-normal line-clamp-2 font-semibold">{{ $f->description }}</p>
                            @endif
                            <div class="pt-1">
                                <a href="{{ route('public.format.download', $f->id) }}" class="inline-flex items-center gap-1 text-xs text-amber-700 font-extrabold hover:underline">
                                    <i class="fas fa-download text-gray-500"></i> Unduh File Panduan
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-700 italic font-bold">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-300">
                            <i class="fas fa-file-pdf text-base text-gray-500"></i>
                        </div>
                        <p class="text-xs">Belum ada file panduan yang diupload oleh sekolah.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab 3: Exam --}}
        <div id="tab-content-exam" class="tab-content hidden space-y-6">
            {{-- Hasil Ujian / Kelulusan (Only if completed) --}}
            @if($project->status === 'completed' && $project->grade !== null)
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-6 md:p-8 shadow-lg flex flex-col sm:flex-row items-center gap-6 border border-emerald-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.2),transparent_60%)]"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/30 flex items-center justify-center text-3xl shadow-md flex-shrink-0 backdrop-blur-sm border border-white/20">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="space-y-1.5 relative z-10">
                        <h3 class="text-lg md:text-xl font-extrabold">Selamat! Anda Lulus Sidang {{ $entityName }}</h3>
                        <p class="text-xs text-white leading-relaxed font-bold">Ujian/Sidang Anda telah dinilai oleh dewan penguji dengan rincian hasil kelulusan berikut:</p>
                        <div class="inline-flex items-center gap-1.5 bg-white text-emerald-900 px-3.5 py-1.5 rounded-xl mt-2.5 text-xs font-black shadow-md border border-emerald-200">
                            <i class="fas fa-star text-amber-500"></i> Nilai Sidang Akhir: {{ $project->grade }} / 100
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-gray-250 shadow-md p-6 space-y-4">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                    <i class="fas fa-calendar-check text-amber-600"></i> Jadwal Sidang Ujian
                </h3>
                
                @if($project->exam_date)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-300 space-y-2 shadow-sm">
                            <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Tanggal & Waktu</p>
                            <p class="text-xs font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-clock text-amber-600"></i> {{ $project->exam_date->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-300 space-y-2 shadow-sm">
                            <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Lokasi / Ruang Sidang</p>
                            <p class="text-xs font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-location-dot text-amber-600"></i> {{ $project->exam_location }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-300 space-y-2 shadow-sm">
                            <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Dewan Penguji</p>
                            <p class="text-xs font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user-tie text-amber-600"></i> Penguji 1: {{ $project->examiner->full_name ?? 'Belum Ditugaskan' }}
                            </p>
                            @if($project->examiner2_id)
                                <p class="text-xs font-bold text-gray-900 flex items-center gap-2 pt-1 border-t border-gray-250">
                                    <i class="fas fa-user-tie text-amber-600"></i> Penguji 2: {{ $project->examiner2->full_name ?? '-' }}
                                </p>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-300 space-y-2 shadow-sm">
                            <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Guru Pembimbing</p>
                            <p class="text-xs font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user-graduate text-amber-600"></i> {{ $project->advisor->full_name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if($project->grade_notes)
                        <div class="bg-gray-100 border border-gray-300 rounded-2xl p-4 text-xs text-gray-800 mt-2 space-y-1 shadow-sm">
                            <p class="font-extrabold text-gray-900 flex items-center gap-1.5"><i class="fas fa-note-sticky text-gray-500"></i> Catatan/Rekomendasi Dewan Penguji:</p>
                            <p class="italic font-bold">"{{ $project->grade_notes }}"</p>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center text-gray-700 italic font-bold">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 border border-gray-300 shadow-sm">
                            <i class="fas fa-clock text-lg text-gray-500"></i>
                        </div>
                        <p class="text-xs max-w-xs leading-relaxed">Jadwal sidang ujian Anda belum diterbitkan. Pastikan status bimbingan Anda sudah dikonfirmasi "Layak Sidang" oleh pembimbing Anda.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Repropose (Only shown if modal toggled) --}}
@if($project->status === 'rejected')
<div id="repropose-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-black/60" onclick="toggleModal('repropose-modal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-250">
            <div class="bg-white px-6 py-5 border-b border-gray-200 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 text-sm border border-amber-250">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <h3 class="text-base font-extrabold text-gray-900">
                    Pengajuan Judul Baru
                </h3>
            </div>
            <form action="{{ route('siswa.final-project.propose') }}" method="POST">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div class="bg-amber-100 border border-amber-300 text-amber-950 p-4 rounded-2xl text-xs leading-relaxed font-bold shadow-sm">
                        <i class="fas fa-info-circle mr-1 text-amber-700"></i> Pengajuan judul baru ini akan menimpa dan membatalkan usulan judul sebelumnya yang ditolak.
                    </div>
                    <div>
                        <label for="modal-title" class="block text-xs font-extrabold text-gray-800 uppercase mb-1.5 tracking-wider">Judul yang Diusulkan</label>
                        <input type="text" name="title" id="modal-title" required placeholder="Tuliskan judul baru..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    </div>
                    <div>
                        <label for="modal-abstract" class="block text-xs font-extrabold text-gray-800 uppercase mb-1.5 tracking-wider">Abstrak / Rencana Penelitian</label>
                        <textarea name="abstract" id="modal-abstract" rows="5" required placeholder="Jelaskan secara singkat latar belakang, perumusan masalah, dan rencana Anda..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition leading-relaxed"></textarea>
                    </div>
                </div>
                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-2 border-t border-gray-200 rounded-b-3xl">
                    <button type="button" onclick="toggleModal('repropose-modal')" class="bg-white hover:bg-gray-50 text-gray-700 font-extrabold px-5 py-2.5 rounded-xl text-xs border border-gray-300 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-md transition-all">
                        Kirim Pengajuan Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-amber-500', 'text-amber-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-amber-500', 'text-amber-600');

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('tab-content-' + tabName).classList.remove('hidden');
    }

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
</script>
@endsection
