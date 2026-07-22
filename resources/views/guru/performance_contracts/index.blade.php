@extends('layouts.guru')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header Banner Section --}}
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20 border border-emerald-700/40 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 space-y-2">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-emerald-200 text-xs sm:text-sm font-bold uppercase tracking-wider">
                <i class="fas fa-file-signature text-amber-400"></i>
                <span>Modul Kinerja Guru & Staff</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Perjanjian Kinerja Saya</span>
            </h2>
            <p class="text-emerald-100/90 font-medium text-sm sm:text-base max-w-2xl leading-relaxed">
                Kelola dokumen komitmen kinerja riil, pantau tahapan verifikasi secara langsung, serta periksa hasil evaluasi akhir Anda.
            </p>
        </div>
        
        <div class="relative z-10 shrink-0">
            <a href="{{ route('guru.performance_contracts.create') }}" class="inline-flex items-center gap-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-lg shadow-emerald-600/30 hover:shadow-xl hover:-translate-y-0.5 border border-emerald-400/50">
                <i class="fas fa-plus text-base"></i> Buat Kontrak Baru
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-300 text-emerald-900 px-5 py-4 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="bg-emerald-600 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 shadow-md shadow-emerald-600/30">
                <i class="fas fa-check"></i>
            </div>
            <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-2 border-rose-300 text-rose-900 px-5 py-4 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="bg-rose-600 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 shadow-md shadow-rose-600/30">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p class="font-bold text-sm sm:text-base">{{ session('error') }}</p>
        </div>
    @endif

    {{-- TAHAPAN & PROGRESS PENGAJUAN PERJANJIAN KINERJA --}}
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-gray-900 flex items-center gap-2.5">
                    <i class="fas fa-tasks text-emerald-600"></i>
                    <span>Tahapan & Progress Pengajuan Perjanjian Kinerja</span>
                </h3>
                <p class="text-sm sm:text-base text-gray-600 font-medium mt-1">Pantau posisi status dan riwayat tahapan dari setiap dokumen perjanjian yang telah Anda ajukan.</p>
            </div>
        </div>

        @if($contracts->isEmpty())
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl border-2 border-dashed border-gray-300 text-center space-y-5">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 text-2xl shadow-inner">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="max-w-xl mx-auto space-y-2">
                <h4 class="text-lg sm:text-xl font-black text-gray-900">Belum Ada Pengajuan Perjanjian Kinerja</h4>
                <p class="text-gray-600 font-medium text-sm sm:text-base leading-relaxed">
                    Setiap perjanjian kinerja akan melalui 4 (empat) tahapan utama: <strong class="text-gray-900">[1] Pengajuan Kinerja &rarr; [2] Persetujuan Kasek &rarr; [3] Persetujuan Yayasan &rarr; [4] Evaluasi</strong>. Silakan buat kontrak baru terlebih dahulu.
                </p>
            </div>
            <div>
                <a href="{{ route('guru.performance_contracts.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base px-6 py-3 rounded-2xl shadow-md transition-all">
                    <i class="fas fa-plus"></i> Buat Perjanjian Sekarang
                </a>
            </div>
        </div>
        @else
            <div class="space-y-6">
                @foreach($contracts as $contract)
                @php
                    // Penentuan title kontrak
                    $contractTitle = 'Perjanjian Kinerja';
                    $typeBadgeClass = 'bg-gray-100 text-gray-800 border-gray-300';
                    $typeIcon = 'fa-file-alt';
                    if ($contract->contract_type == 'pkg_kejuruan') {
                        $contractTitle = 'Form 2A (Kejuruan/Produktif)';
                        $typeBadgeClass = 'bg-blue-100 text-blue-900 border-blue-400';
                        $typeIcon = 'fa-tools text-blue-600';
                    } elseif ($contract->contract_type == 'pkg_umum') {
                        $contractTitle = 'Form 2B (Mata Pelajaran Umum)';
                        $typeBadgeClass = 'bg-indigo-100 text-indigo-900 border-indigo-400';
                        $typeIcon = 'fa-book-open text-indigo-600';
                    } else {
                        $posName = $contract->position ? $contract->position->position_name : 'Jabatan Tambahan';
                        $contractTitle = 'Form 4 (' . $posName . ')';
                        $typeBadgeClass = 'bg-amber-100 text-amber-900 border-amber-400';
                        $typeIcon = 'fa-briefcase text-amber-600';
                    }

                    // Penentuan status tiap step (1: Pengajuan, 2: Kasek, 3: Yayasan, 4: Evaluasi)
                    $st = $contract->status;
                    
                    // Step 1: Pengajuan
                    $step1Status = 'done'; // karena sudah tersimpan
                    $step1Text = 'Selesai Diajukan';
                    $step1Date = $contract->created_at->format('d M Y');

                    // Step 2: Kasek
                    $step2Status = 'pending';
                    $step2Text = 'Menunggu Pengajuan';
                    if ($st == 'submitted_to_kepsek') {
                        $step2Status = 'active';
                        $step2Text = 'Sedang Diperiksa Kasek';
                    } elseif (in_array($st, ['approved_by_kepsek', 'approved_by_yayasan'])) {
                        $step2Status = 'done';
                        $step2Text = 'Disetujui Kasek';
                    } elseif ($st == 'rejected') {
                        $step2Status = 'rejected';
                        $step2Text = 'Ditolak / Dikembalikan';
                    }

                    // Step 3: Yayasan
                    $step3Status = 'pending';
                    $step3Text = 'Menunggu Kasek';
                    if ($st == 'approved_by_kepsek') {
                        $step3Status = 'active';
                        $step3Text = 'Verifikasi Ketua Yayasan';
                    } elseif ($st == 'approved_by_yayasan') {
                        $step3Status = 'done';
                        $step3Text = 'Disetujui Ketua Yayasan';
                    } elseif ($st == 'rejected') {
                        $step3Status = 'rejected';
                        $step3Text = 'Proses Terhenti';
                    }

                    // Step 4: Evaluasi
                    $step4Status = 'pending';
                    $step4Text = 'Belum Tahap Evaluasi';
                    $approvedEval = $contract->evaluations ? $contract->evaluations->where('status', 'approved_by_yayasan')->first() : null;
                    $submittedEval = $contract->evaluations ? $contract->evaluations->where('status', 'submitted_to_yayasan')->first() : null;

                    if ($st == 'approved_by_yayasan') {
                        if ($approvedEval && $approvedEval->score > 0) {
                            $step4Status = 'done';
                            $step4Text = 'Selesai Dinilai (' . number_format($approvedEval->score, 2) . ')';
                        } elseif ($submittedEval) {
                            $step4Status = 'active';
                            $step4Text = 'Evaluasi Menunggu ACC';
                        } else {
                            $step4Status = 'active';
                            $step4Text = 'Masa Pelaksanaan & Target';
                        }
                    } elseif ($st == 'rejected') {
                        $step4Status = 'rejected';
                        $step4Text = '-';
                    }
                @endphp

                <div class="bg-white rounded-3xl shadow-xl border-2 {{ $st == 'rejected' ? 'border-rose-300' : 'border-indigo-100' }} overflow-hidden">
                    {{-- Header Top Bar dari Card Progress --}}
                    <div class="bg-gradient-to-r from-slate-100 via-gray-100 to-slate-100 border-b-2 border-gray-200 px-6 sm:px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-white border-2 border-gray-300 flex items-center justify-center text-xl shadow-sm shrink-0">
                                <i class="fas {{ $typeIcon }}"></i>
                            </div>
                            <div>
                                <div class="text-xs font-black text-gray-500 uppercase tracking-wider">Title Progres Pengajuan</div>
                                <h4 class="text-lg sm:text-xl font-black text-gray-900">{{ $contractTitle }}</h4>
                                <div class="text-xs sm:text-sm font-bold text-indigo-600 mt-0.5">
                                    <i class="fas fa-calendar-alt mr-1"></i> Tahun Pelajaran {{ $contract->academicYear->year ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 self-start sm:self-center">
                            @if($st == 'submitted_to_kepsek')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-400 shadow-sm">
                                    <i class="fas fa-clock text-amber-600 animate-pulse"></i> Menunggu Kasek
                                </span>
                            @elseif($st == 'approved_by_kepsek')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-400 shadow-sm">
                                    <i class="fas fa-spinner text-blue-600 animate-spin"></i> Menunggu Yayasan
                                </span>
                            @elseif($st == 'approved_by_yayasan')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-emerald-100 text-emerald-900 border-2 border-emerald-400 shadow-sm">
                                    <i class="fas fa-check-circle text-emerald-600"></i> ACC Yayasan
                                </span>
                            @elseif($st == 'rejected')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-rose-100 text-rose-900 border-2 border-rose-400 shadow-sm">
                                    <i class="fas fa-times-circle text-rose-600"></i> Dikembalikan
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                    {{ $st }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Stepper Progress Bar Horizontal (4 Tahapan Bulat & Kontras Tinggi) --}}
                    <div class="p-6 sm:p-8 bg-slate-50/50">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative">
                            {{-- Line connector horizontal (Membelah tengah lingkaran di desktop) --}}
                            <div class="hidden sm:block absolute top-6 left-[12%] right-[12%] h-1.5 bg-slate-200 rounded-full z-0">
                                @php
                                    $progressWidth = '0%';
                                    if ($step4Status == 'done') { $progressWidth = '100%'; }
                                    elseif ($step3Status == 'done' || $step4Status == 'active') { $progressWidth = '75%'; }
                                    elseif ($step2Status == 'done' || $step3Status == 'active') { $progressWidth = '50%'; }
                                    elseif ($step1Status == 'done' || $step2Status == 'active') { $progressWidth = '25%'; }
                                @endphp
                                <div class="h-full bg-gradient-to-r from-emerald-500 via-indigo-600 to-purple-600 rounded-full transition-all duration-500 shadow-sm" style="width: {{ $progressWidth }}"></div>
                            </div>

                            {{-- [1] Pengajuan Kinerja --}}
                            <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                                <div class="w-12 h-12 rounded-full bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-600">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-emerald-700 uppercase tracking-wider block mb-0.5">[1] Tahap 1</span>
                                    <h5 class="font-black text-base sm:text-lg text-slate-900">Pengajuan Kinerja</h5>
                                    <div class="mt-1">
                                        <span class="text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-full border border-emerald-300 inline-block">{{ $step1Text }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-600 block mt-1">{{ $step1Date }}</span>
                                </div>
                            </div>

                            {{-- [2] Persetujuan Kasek --}}
                            <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                                @if($step2Status == 'done')
                                    <div class="w-12 h-12 rounded-full bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-600">
                                        <i class="fas fa-check"></i>
                                    </div>
                                @elseif($step2Status == 'active')
                                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-600/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-indigo-200 border-2 border-indigo-600 animate-pulse">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @elseif($step2Status == 'rejected')
                                    <div class="w-12 h-12 rounded-full bg-rose-600 text-white shadow-lg shadow-rose-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-rose-100 border-2 border-rose-600">
                                        <i class="fas fa-times"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-700">
                                        2
                                    </div>
                                @endif
                                <div>
                                    <span class="text-xs font-black {{ $step2Status == 'done' ? 'text-emerald-700' : ($step2Status == 'active' ? 'text-indigo-700' : ($step2Status == 'rejected' ? 'text-rose-700' : 'text-slate-700')) }} uppercase tracking-wider block mb-0.5">[2] Tahap 2</span>
                                    <h5 class="font-black text-base sm:text-lg text-slate-900">Persetujuan Kasek</h5>
                                    <div class="mt-1">
                                        @if($step2Status == 'done')
                                            <span class="text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-full border border-emerald-300 inline-block">{{ $step2Text }}</span>
                                        @elseif($step2Status == 'active')
                                            <span class="text-xs sm:text-sm font-black text-indigo-900 bg-indigo-100 px-3 py-1 rounded-full border border-indigo-300 inline-block animate-pulse">{{ $step2Text }}</span>
                                        @elseif($step2Status == 'rejected')
                                            <span class="text-xs sm:text-sm font-bold text-rose-800 bg-rose-100 px-3 py-1 rounded-full border border-rose-300 inline-block">{{ $step2Text }}</span>
                                        @else
                                            <span class="text-xs sm:text-sm font-bold text-slate-800 bg-slate-200 px-3 py-1 rounded-full border border-slate-400 inline-block">{{ $step2Text }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- [3] Persetujuan Yayasan --}}
                            <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                                @if($step3Status == 'done')
                                    <div class="w-12 h-12 rounded-full bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-600">
                                        <i class="fas fa-check"></i>
                                    </div>
                                @elseif($step3Status == 'active')
                                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-600/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-indigo-200 border-2 border-indigo-600 animate-pulse">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @elseif($step3Status == 'rejected')
                                    <div class="w-12 h-12 rounded-full bg-rose-600 text-white shadow-lg shadow-rose-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-rose-100 border-2 border-rose-600">
                                        <i class="fas fa-times"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-700">
                                        3
                                    </div>
                                @endif
                                <div>
                                    <span class="text-xs font-black {{ $step3Status == 'done' ? 'text-emerald-700' : ($step3Status == 'active' ? 'text-indigo-700' : ($step3Status == 'rejected' ? 'text-rose-700' : 'text-slate-700')) }} uppercase tracking-wider block mb-0.5">[3] Tahap 3</span>
                                    <h5 class="font-black text-base sm:text-lg text-slate-900">Persetujuan Yayasan</h5>
                                    <div class="mt-1">
                                        @if($step3Status == 'done')
                                            <span class="text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-full border border-emerald-300 inline-block">{{ $step3Text }}</span>
                                        @elseif($step3Status == 'active')
                                            <span class="text-xs sm:text-sm font-black text-indigo-900 bg-indigo-100 px-3 py-1 rounded-full border border-indigo-300 inline-block animate-pulse">{{ $step3Text }}</span>
                                        @elseif($step3Status == 'rejected')
                                            <span class="text-xs sm:text-sm font-bold text-rose-800 bg-rose-100 px-3 py-1 rounded-full border border-rose-300 inline-block">{{ $step3Text }}</span>
                                        @else
                                            <span class="text-xs sm:text-sm font-bold text-slate-800 bg-slate-200 px-3 py-1 rounded-full border border-slate-400 inline-block">{{ $step3Text }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- [4] Evaluasi --}}
                            <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                                @if($step4Status == 'done')
                                    <div class="w-12 h-12 rounded-full bg-purple-600 text-white shadow-lg shadow-purple-600/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-purple-100 border-2 border-purple-600">
                                        <i class="fas fa-star text-amber-300"></i>
                                    </div>
                                @elseif($step4Status == 'active')
                                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-600/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-indigo-200 border-2 border-indigo-600 animate-pulse">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                @elseif($step4Status == 'rejected')
                                    <div class="w-12 h-12 rounded-full bg-slate-700 text-slate-300 flex items-center justify-center text-lg font-black shrink-0 border-2 border-slate-600">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-700">
                                        4
                                    </div>
                                @endif
                                <div>
                                    <span class="text-xs font-black {{ $step4Status == 'done' ? 'text-purple-700' : ($step4Status == 'active' ? 'text-indigo-700' : 'text-slate-700') }} uppercase tracking-wider block mb-0.5">[4] Tahap 4</span>
                                    <h5 class="font-black text-base sm:text-lg text-slate-900">Evaluasi & Penilaian</h5>
                                    <div class="mt-1">
                                        @if($step4Status == 'done')
                                            <span class="text-xs sm:text-sm font-bold text-purple-900 bg-purple-100 px-3 py-1 rounded-full border border-purple-300 inline-block">{{ $step4Text }}</span>
                                        @elseif($step4Status == 'active')
                                            <span class="text-xs sm:text-sm font-black text-indigo-900 bg-indigo-100 px-3 py-1 rounded-full border border-indigo-300 inline-block animate-pulse">{{ $step4Text }}</span>
                                        @elseif($step4Status == 'rejected')
                                            <span class="text-xs sm:text-sm font-bold text-slate-700 bg-slate-200 px-3 py-1 rounded-full border border-slate-400 inline-block">-</span>
                                        @else
                                            <span class="text-xs sm:text-sm font-bold text-slate-800 bg-slate-200 px-3 py-1 rounded-full border border-slate-400 inline-block">{{ $step4Text }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Penolakan jika dikembalikan --}}
                        @if($st == 'rejected' && !empty($contract->notes))
                            <div class="mt-6 bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5">
                                <div class="bg-rose-600 text-white w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div>
                                    <h6 class="font-black text-rose-900 text-base">Catatan Pengembalian / Penolakan:</h6>
                                    <p class="text-rose-800 text-sm sm:text-base font-medium mt-1 leading-relaxed">{{ $contract->notes }}</p>
                                    <div class="mt-3">
                                        <a href="{{ route('guru.performance_contracts.edit', $contract->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-md transition-all">
                                            <i class="fas fa-pen"></i> Perbaiki & Ajukan Ulang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tombol Aksi Langsung pada Card Progres --}}
                        <div class="mt-6 pt-5 border-t-2 border-gray-100 flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('guru.performance_contracts.show', $contract->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border-2 border-indigo-200 hover:border-indigo-600 font-bold text-sm sm:text-base transition-all shadow-sm">
                                <i class="fas fa-file-contract"></i> Lihat Dokumen Lengkap
                            </a>
                            @if($st == 'approved_by_yayasan')
                                <a href="{{ route('guru.performance_contracts.print', $contract->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base transition-all shadow-md shadow-emerald-600/20">
                                    <i class="fas fa-print"></i> Cetak Pakta Integritas
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Table Card (Daftar Semua Perjanjian) --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-lg sm:text-xl font-black text-gray-900 flex items-center gap-2.5">
                <i class="fas fa-table text-indigo-600"></i>
                <span>Tabel Riwayat Perjanjian Kinerja</span>
            </h3>
            <span class="text-xs sm:text-sm font-semibold text-gray-500">Daftar lengkap arsip perjanjian</span>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500"></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-100 via-indigo-50/40 to-slate-100 border-b-2 border-gray-200">
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Tahun Ajaran</th>
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Tipe Kontrak</th>
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Status Persetujuan</th>
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Hasil Evaluasi Akhir</th>
                            <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($contracts as $contract)
                        <tr class="hover:bg-indigo-50/40 transition-all duration-200 group">
                            <td class="px-6 py-5">
                                <div class="font-black text-base sm:text-lg text-gray-900">{{ $contract->academicYear->year }}</div>
                                <div class="text-sm font-bold text-indigo-600 mt-0.5">Semester {{ $contract->academicYear->semester }}</div>
                            </td>
                            <td class="px-6 py-5">
                                @if($contract->contract_type == 'pkg_kejuruan')
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-300 shadow-sm">
                                        <i class="fas fa-tools text-blue-600"></i> Form 2A (Kejuruan)
                                    </span>
                                @elseif($contract->contract_type == 'pkg_umum')
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-indigo-100 text-indigo-900 border-2 border-indigo-300 shadow-sm">
                                        <i class="fas fa-book-open text-indigo-600"></i> Form 2B (Umum)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-300 shadow-sm">
                                        <i class="fas fa-briefcase text-amber-600"></i> Form 4 (Jabatan)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-base text-gray-800 font-bold">
                                {{ $contract->position ? $contract->position->position_name : '-' }}
                            </td>
                            <td class="px-6 py-5">
                                @if($contract->status == 'draft')
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 border-2 border-gray-300 shadow-sm"><i class="fas fa-file-alt"></i> Draft</span>
                                @elseif($contract->status == 'submitted_to_kepsek')
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-400 shadow-sm">
                                        <i class="fas fa-clock text-amber-600 animate-pulse"></i> [1] &rarr; [2] Kasek
                                    </span>
                                @elseif($contract->status == 'approved_by_kepsek')
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-400 shadow-sm">
                                        <i class="fas fa-spinner text-blue-600 animate-spin"></i> [2] &rarr; [3] Yayasan
                                    </span>
                                @elseif($contract->status == 'approved_by_yayasan')
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-emerald-100 text-emerald-900 border-2 border-emerald-400 shadow-sm">
                                        <i class="fas fa-check-circle text-emerald-600"></i> Disetujui Penuh
                                    </span>
                                @elseif($contract->status == 'rejected')
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-rose-100 text-rose-900 border-2 border-rose-400 shadow-sm">
                                        <i class="fas fa-times-circle text-rose-600"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $approvedEval = $contract->evaluations ? $contract->evaluations->where('status', 'approved_by_yayasan')->first() : null;
                                    $submittedEval = $contract->evaluations ? $contract->evaluations->where('status', 'submitted_to_yayasan')->first() : null;
                                @endphp
                                @if($approvedEval && $approvedEval->score > 0)
                                    @php
                                        $s = $approvedEval->score;
                                        $label = ($s >= 4.5) ? 'Sangat Baik' : (($s >= 3.5) ? 'Baik' : (($s >= 2.5) ? 'Cukup' : 'Kurang'));
                                        $badgeClass = ($s >= 4.5) ? 'bg-purple-100 text-purple-900 border-purple-400' : (($s >= 3.5) ? 'bg-emerald-100 text-emerald-900 border-emerald-400' : 'bg-amber-100 text-amber-900 border-amber-400');
                                    @endphp
                                    <a href="{{ route('guru.performance_contracts.show', $contract->id) }}" class="inline-flex flex-col items-start gap-1 p-3 rounded-2xl {{ $badgeClass }} border-2 hover:shadow-md transition-all">
                                        <div class="flex items-center gap-2 font-black text-base">
                                            <span>{{ number_format($s, 2) }}</span>
                                            <i class="fas fa-star text-amber-400"></i>
                                            <span class="text-xs font-black px-2 py-0.5 bg-white/90 rounded-md">{{ $label }}</span>
                                        </div>
                                        <span class="text-xs font-bold opacity-90"><i class="fas fa-check-circle mr-1"></i> [4] Evaluasi ACC Yayasan</span>
                                    </a>
                                @elseif($submittedEval)
                                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-400 shadow-sm">
                                        <i class="fas fa-clock text-amber-600 animate-pulse"></i> [4] Menunggu ACC Yayasan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300 shadow-sm"><i class="fas fa-hourglass-start text-slate-500"></i> Belum Dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-2.5">
                                    <a href="{{ route('guru.performance_contracts.show', $contract->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border-2 border-indigo-200 hover:border-indigo-600 transition-all shadow-sm tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye text-base"></i>
                                    </a>
                                    
                                    @if(in_array($contract->status, ['draft', 'submitted_to_kepsek', 'rejected']))
                                        <a href="{{ route('guru.performance_contracts.edit', $contract->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white border-2 border-amber-200 hover:border-amber-600 transition-all shadow-sm tooltip" title="Edit Kontrak">
                                            <i class="fas fa-pen text-base"></i>
                                        </a>
                                        <form action="{{ route('guru.performance_contracts.destroy', $contract->id) }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border-2 border-rose-200 hover:border-rose-600 transition-all shadow-sm tooltip" title="Hapus Kontrak">
                                                <i class="fas fa-trash-alt text-base"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($contract->status == 'approved_by_yayasan')
                                        <a href="{{ route('guru.performance_contracts.print', $contract->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm border-2 border-emerald-600 transition-all shadow-md tooltip" title="Cetak Pakta Integritas">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-slate-100 border-2 border-slate-200 mb-5 shadow-inner">
                                    <i class="fas fa-file-signature text-3xl text-slate-400"></i>
                                </div>
                                <h3 class="text-gray-900 font-black text-xl mb-2">Belum Ada Kontrak</h3>
                                <p class="text-gray-600 font-medium text-base mb-6 max-w-lg mx-auto leading-relaxed">Anda belum membuat Perjanjian Kinerja untuk Tahun Ajaran ini. Silakan mulai buat komitmen baru sekarang.</p>
                                <a href="{{ route('guru.performance_contracts.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl text-base font-bold transition-all shadow-md">
                                    <i class="fas fa-plus"></i> Buat Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
