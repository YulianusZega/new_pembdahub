@extends('layouts.admin')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto pb-12">
    {{-- Header Banner --}}
    @php
        $isYayasanView = $isYayasanView ?? (auth()->user()->isSuperAdmin() || auth()->user()->isYayasan() || request()->routeIs('yayasan.*'));
        $routePrefix = $isYayasanView ? 'yayasan.' : 'admin.';
    @endphp
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50/50 to-white rounded-3xl p-6 sm:p-8 text-slate-900 shadow-xl border-2 border-emerald-300 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-black uppercase tracking-wider shadow-sm">
                <i class="fas fa-file-contract"></i>
                <span>Verifikasi Dokumen Resmi</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pemeriksaan Perjanjian Kinerja</h2>
            <p class="text-slate-700 font-bold text-sm sm:text-base max-w-xl leading-relaxed">Evaluasi dan tinjau rincian target kinerja riil yang diajukan oleh guru atau tenaga kependidikan.</p>
        </div>
        
        <div class="relative z-10 shrink-0 flex flex-wrap items-center gap-3">
            <a href="{{ route($routePrefix . 'performance_contracts.print', $contract->id) }}" target="_blank" class="inline-flex items-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-md shadow-emerald-600/30">
                <i class="fas fa-print"></i> Cetak Dokumen Resmi
            </a>
            <a href="{{ route($routePrefix . 'performance_contracts.index') }}" class="inline-flex items-center gap-2.5 bg-white hover:bg-emerald-50 text-emerald-950 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm border-2 border-emerald-200">
                <i class="fas fa-arrow-left text-emerald-600"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
    @php
        // Penentuan status tiap step (1: Pengajuan, 2: Kasek, 3: Yayasan, 4: Evaluasi)
        $st = $contract->status;
        
        // Step 1: Pengajuan
        $step1Status = 'done';
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

        $contractTitle = 'Perjanjian Kinerja';
        $typeIcon = 'fa-file-alt text-gray-600';
        if ($contract->contract_type == 'pkg_kejuruan') {
            $contractTitle = 'Form 2A (Kejuruan/Produktif)';
            $typeIcon = 'fa-tools text-blue-600';
        } elseif ($contract->contract_type == 'pkg_umum') {
            $contractTitle = 'Form 2B (Mata Pelajaran Umum)';
            $typeIcon = 'fa-book-open text-indigo-600';
        } else {
            $posName = $contract->position ? $contract->position->position_name : 'Jabatan Tambahan';
            $contractTitle = 'Form 4 (' . $posName . ')';
            $typeIcon = 'fa-briefcase text-amber-600';
        }
    @endphp

    @php
        $headerBgClass = 'bg-gradient-to-r from-indigo-50 via-purple-50/50 to-white text-slate-900 border-l-8 border-indigo-600';
        $iconBgClass = 'bg-indigo-600 text-white border-2 border-indigo-300 shadow-md shadow-indigo-500/20';
        $titleTextClass = 'text-indigo-950';
        $badgeTextClass = 'text-indigo-700';
        $yearBadgeClass = 'bg-indigo-100/80 text-indigo-900 border border-indigo-300';
        if ($contract->contract_type == 'pkg_kejuruan') {
            $headerBgClass = 'bg-gradient-to-r from-blue-50 via-cyan-50/50 to-white text-slate-900 border-l-8 border-blue-600';
            $iconBgClass = 'bg-blue-600 text-white border-2 border-blue-300 shadow-md shadow-blue-500/20';
            $titleTextClass = 'text-blue-950';
            $badgeTextClass = 'text-blue-700';
            $yearBadgeClass = 'bg-blue-100/80 text-blue-900 border border-blue-300';
        } elseif ($contract->contract_type == 'pkg_umum') {
            $headerBgClass = 'bg-gradient-to-r from-indigo-50 via-purple-50/50 to-white text-slate-900 border-l-8 border-indigo-600';
            $iconBgClass = 'bg-indigo-600 text-white border-2 border-indigo-300 shadow-md shadow-indigo-500/20';
            $titleTextClass = 'text-indigo-950';
            $badgeTextClass = 'text-indigo-700';
            $yearBadgeClass = 'bg-indigo-100/80 text-indigo-900 border border-indigo-300';
        } else {
            $headerBgClass = 'bg-gradient-to-r from-amber-50 via-orange-50/50 to-white text-slate-900 border-l-8 border-amber-500';
            $iconBgClass = 'bg-amber-600 text-white border-2 border-amber-300 shadow-md shadow-amber-500/20';
            $titleTextClass = 'text-amber-950';
            $badgeTextClass = 'text-amber-800';
            $yearBadgeClass = 'bg-amber-100/80 text-amber-900 border border-amber-300';
        }
    @endphp

    {{-- Progress Tracker Card Khusus Dokumen Ini --}}
    <div class="bg-white rounded-3xl shadow-xl border-2 {{ $st == 'rejected' ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200 ring-4 ring-slate-100' }} overflow-hidden transition-all duration-300 hover:shadow-2xl">
        <div class="{{ $headerBgClass }} border-b-2 border-slate-200 px-6 sm:px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $iconBgClass }} flex items-center justify-center text-2xl shrink-0">
                    <i class="fas {{ $typeIcon }} text-white"></i>
                </div>
                <div>
                    <div class="text-xs font-black {{ $badgeTextClass }} uppercase tracking-wider">Status Tahapan Pengajuan</div>
                    <h3 class="text-lg sm:text-2xl font-black {{ $titleTextClass }} tracking-tight">{{ $contractTitle }}</h3>
                    <div class="text-xs sm:text-sm font-bold mt-1 flex items-center gap-2">
                        <span class="{{ $yearBadgeClass }} font-black px-3 py-0.5 rounded-lg"><i class="fas fa-calendar-alt mr-1"></i> TP. {{ $contract->academicYear->year ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 self-start sm:self-center">
                @if($st == 'submitted_to_kepsek')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-black bg-gradient-to-r from-amber-400 to-orange-500 text-slate-950 border-2 border-amber-200 shadow-lg shadow-amber-500/30">
                        <i class="fas fa-clock animate-pulse"></i> Menunggu Kasek
                    </span>
                @elseif($st == 'approved_by_kepsek')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-black bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-2 border-blue-300 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-spinner animate-spin"></i> Menunggu Yayasan
                    </span>
                @elseif($st == 'approved_by_yayasan')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-black bg-gradient-to-r from-emerald-500 to-teal-600 text-white border-2 border-emerald-300 shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-check-circle"></i> ACC Yayasan
                    </span>
                @elseif($st == 'rejected')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-black bg-gradient-to-r from-rose-500 to-red-600 text-white border-2 border-rose-300 shadow-lg shadow-rose-500/30">
                        <i class="fas fa-times-circle"></i> Dikembalikan
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 border border-gray-300">
                        {{ $st }}
                    </span>
                @endif
            </div>
        </div>

        {{-- 4 Tahapan Progress Tracker Bulat & Kontras Tinggi --}}
        <div class="p-6 sm:p-8 bg-gradient-to-b from-slate-50 via-white to-slate-50/80">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative">
                {{-- Line connector horizontal --}}
                <div class="hidden sm:block absolute top-7 left-[12%] right-[12%] h-2 bg-slate-200 border border-slate-300 rounded-full z-0 shadow-inner">
                    @php
                        $progressWidth = '0%';
                        if ($step4Status == 'done') { $progressWidth = '100%'; }
                        elseif ($step3Status == 'done' || $step4Status == 'active') { $progressWidth = '75%'; }
                        elseif ($step2Status == 'done' || $step3Status == 'active') { $progressWidth = '50%'; }
                        elseif ($step1Status == 'done' || $step2Status == 'active') { $progressWidth = '25%'; }
                    @endphp
                    <div class="h-full bg-gradient-to-r from-emerald-500 via-indigo-500 to-purple-600 rounded-full transition-all duration-700 shadow-md" style="width: {{ $progressWidth }}"></div>
                </div>

                {{-- [1] Pengajuan Kinerja --}}
                <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-400">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <span class="text-xs font-black text-emerald-700 uppercase tracking-wider block mb-0.5">[1] Tahap 1</span>
                        <h5 class="font-black text-base sm:text-lg text-slate-900">Pengajuan Kinerja</h5>
                        <div class="mt-1">
                            <span class="text-xs sm:text-sm font-black text-emerald-950 bg-emerald-100 px-3.5 py-1 rounded-full border border-emerald-400 inline-block shadow-sm">{{ $step1Text }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-600 block mt-1">{{ $step1Date }}</span>
                    </div>
                </div>

                {{-- [2] Persetujuan Kasek --}}
                <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                    @if($step2Status == 'done')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-400">
                            <i class="fas fa-check"></i>
                        </div>
                    @elseif($step2Status == 'active')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-xl shadow-indigo-500/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-indigo-200 border-2 border-indigo-400 animate-pulse scale-110">
                            <i class="fas fa-clock"></i>
                        </div>
                    @elseif($step2Status == 'rejected')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-600 to-red-700 text-white shadow-lg shadow-rose-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-rose-100 border-2 border-rose-400">
                            <i class="fas fa-times"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-600">
                            2
                        </div>
                    @endif
                    <div>
                        <span class="text-xs font-black {{ $step2Status == 'done' ? 'text-emerald-700' : ($step2Status == 'active' ? 'text-indigo-700' : ($step2Status == 'rejected' ? 'text-rose-700' : 'text-slate-700')) }} uppercase tracking-wider block mb-0.5">[2] Tahap 2</span>
                        <h5 class="font-black text-base sm:text-lg text-slate-900">Persetujuan Kasek</h5>
                        <div class="mt-1">
                            @if($step2Status == 'done')
                                <span class="text-xs sm:text-sm font-black text-emerald-950 bg-emerald-100 px-3.5 py-1 rounded-full border border-emerald-400 inline-block shadow-sm">{{ $step2Text }}</span>
                            @elseif($step2Status == 'active')
                                <span class="text-xs sm:text-sm font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-1.5 rounded-full border border-indigo-300 inline-block shadow-md shadow-indigo-500/20 animate-pulse">{{ $step2Text }}</span>
                            @elseif($step2Status == 'rejected')
                                <span class="text-xs sm:text-sm font-black text-white bg-gradient-to-r from-rose-500 to-red-600 px-3.5 py-1 rounded-full border border-rose-400 inline-block shadow-sm">{{ $step2Text }}</span>
                            @else
                                <span class="text-xs sm:text-sm font-black text-slate-800 bg-white px-3.5 py-1 rounded-full border-2 border-slate-300 inline-block shadow-sm">{{ $step2Text }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- [3] Persetujuan Yayasan --}}
                <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                    @if($step3Status == 'done')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-emerald-100 border-2 border-emerald-400">
                            <i class="fas fa-check"></i>
                        </div>
                    @elseif($step3Status == 'active')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-xl shadow-amber-500/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-amber-200 border-2 border-amber-400 animate-pulse scale-110">
                            <i class="fas fa-clock"></i>
                        </div>
                    @elseif($step3Status == 'rejected')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-600 to-red-700 text-white shadow-lg shadow-rose-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-rose-100 border-2 border-rose-400">
                            <i class="fas fa-times"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-600">
                            3
                        </div>
                    @endif
                    <div>
                        <span class="text-xs font-black {{ $step3Status == 'done' ? 'text-emerald-700' : ($step3Status == 'active' ? 'text-amber-700' : ($step3Status == 'rejected' ? 'text-rose-700' : 'text-slate-700')) }} uppercase tracking-wider block mb-0.5">[3] Tahap 3</span>
                        <h5 class="font-black text-base sm:text-lg text-slate-900">Persetujuan Yayasan</h5>
                        <div class="mt-1">
                            @if($step3Status == 'done')
                                <span class="text-xs sm:text-sm font-black text-emerald-950 bg-emerald-100 px-3.5 py-1 rounded-full border border-emerald-400 inline-block shadow-sm">{{ $step3Text }}</span>
                            @elseif($step3Status == 'active')
                                <span class="text-xs sm:text-sm font-black text-slate-950 bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-1.5 rounded-full border border-amber-300 inline-block shadow-md shadow-amber-500/20 animate-pulse">{{ $step3Text }}</span>
                            @elseif($step3Status == 'rejected')
                                <span class="text-xs sm:text-sm font-black text-white bg-gradient-to-r from-rose-500 to-red-600 px-3.5 py-1 rounded-full border border-rose-400 inline-block shadow-sm">{{ $step3Text }}</span>
                            @else
                                <span class="text-xs sm:text-sm font-black text-slate-800 bg-white px-3.5 py-1 rounded-full border-2 border-slate-300 inline-block shadow-sm">{{ $step3Text }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- [4] Evaluasi --}}
                <div class="relative z-10 flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-2.5">
                    @if($step4Status == 'done')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-indigo-700 text-white shadow-lg shadow-purple-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-purple-100 border-2 border-purple-400">
                            <i class="fas fa-star text-amber-300"></i>
                        </div>
                    @elseif($step4Status == 'active')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 text-white shadow-xl shadow-purple-500/40 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-purple-200 border-2 border-purple-400 animate-pulse scale-110">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    @elseif($step4Status == 'rejected')
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-600 to-red-700 text-white shadow-lg shadow-rose-500/30 flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-rose-100 border-2 border-rose-400">
                            <i class="fas fa-times"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-slate-800 text-white shadow-md flex items-center justify-center text-lg font-black shrink-0 ring-4 ring-slate-200 border-2 border-slate-600">
                            4
                        </div>
                    @endif
                    <div>
                        <span class="text-xs font-black {{ $step4Status == 'done' ? 'text-purple-700' : ($step4Status == 'active' ? 'text-indigo-700' : ($step4Status == 'rejected' ? 'text-rose-700' : 'text-slate-700')) }} uppercase tracking-wider block mb-0.5">[4] Tahap 4</span>
                        <h5 class="font-black text-base sm:text-lg text-slate-900">Evaluasi Kinerja</h5>
                        <div class="mt-1">
                            @if($step4Status == 'done')
                                <span class="text-xs sm:text-sm font-black text-purple-950 bg-purple-100 px-3.5 py-1 rounded-full border border-purple-400 inline-block shadow-sm">{{ $step4Text }}</span>
                            @elseif($step4Status == 'active')
                                <span class="text-xs sm:text-sm font-black text-white bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-1.5 rounded-full border border-purple-300 inline-block shadow-md shadow-purple-500/20 animate-pulse">{{ $step4Text }}</span>
                            @elseif($step4Status == 'rejected')
                                <span class="text-xs sm:text-sm font-black text-white bg-gradient-to-r from-rose-500 to-red-600 px-3.5 py-1 rounded-full border border-rose-400 inline-block shadow-sm">{{ $step4Text }}</span>
                            @else
                                <span class="text-xs sm:text-sm font-black text-slate-800 bg-white px-3.5 py-1 rounded-full border-2 border-slate-300 inline-block shadow-sm">{{ $step4Text }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Kolom Kiri: Info Pegawai & Status --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                <div class="bg-gradient-to-r from-indigo-50 via-purple-50/50 to-indigo-50 border-b-2 border-indigo-100/80 px-6 py-5">
                    <h3 class="font-black text-indigo-950 text-lg sm:text-xl flex items-center gap-3">
                        <i class="fas fa-id-badge text-indigo-600 text-xl"></i> Informasi Pegawai
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-1">Nama Lengkap</div>
                        <div class="font-black text-lg sm:text-xl text-gray-900">{{ $contract->employee->full_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-1">NIP / NUPTK</div>
                        <div class="font-bold text-base text-gray-800">{{ $contract->employee->nip ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-1">Unit Sekolah</div>
                        <div class="font-extrabold text-base text-gray-800 inline-flex items-center gap-2 bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-300/80 mt-1">
                            <i class="fas fa-school text-indigo-600"></i>
                            {{ $contract->school->name ?? '-' }}
                        </div>
                    </div>
                    <hr class="border-gray-200">
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-2">Tipe Kontrak</div>
                        @if($contract->contract_type == 'pkg_kejuruan')
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-300 shadow-sm w-full justify-center">
                                <i class="fas fa-tools text-blue-600"></i> Form 2A (Kejuruan)
                            </span>
                        @elseif($contract->contract_type == 'pkg_umum')
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-indigo-100 text-indigo-900 border-2 border-indigo-300 shadow-sm w-full justify-center">
                                <i class="fas fa-book-open text-indigo-600"></i> Form 2B (Mapel Umum)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-300 shadow-sm w-full justify-center">
                                <i class="fas fa-briefcase text-amber-600"></i> Form 4 (Jabatan)
                            </span>
                        @endif
                    </div>
                    @if($contract->contract_type == 'jabatan_tambahan')
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-1">Jabatan Tambahan</div>
                        <div class="font-bold text-base text-gray-900 bg-amber-50 px-4 py-2.5 rounded-xl border border-amber-200">{{ $contract->position->position_name ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Area Approval --}}
            @php
                $user = auth()->user();
                $canApprove = false;
                
                if ($isYayasanView && $contract->status == 'approved_by_kepsek') {
                    $canApprove = true; // Yayasan memproses setelah Kepsek
                } elseif (!$isYayasanView && $contract->status == 'submitted_to_kepsek') {
                    $canApprove = true; // Kepsek memproses yang baru masuk
                }
            @endphp

            @if($canApprove)
            <div class="bg-white rounded-3xl shadow-xl border-2 border-indigo-400 overflow-hidden relative">
                <div class="h-2 bg-gradient-to-r from-emerald-500 via-indigo-500 to-purple-500"></div>
                <div class="p-6">
                    <h3 class="font-black text-gray-900 text-lg sm:text-xl mb-5 flex items-center gap-3">
                        <i class="fas fa-gavel text-indigo-600 text-xl"></i> Tindakan Persetujuan
                    </h3>
                    
                    <form action="{{ route($routePrefix . 'performance_contracts.process', $contract->id) }}" method="POST" id="approvalForm">
                        @csrf
                        <input type="hidden" name="action" id="actionInput" value="">
                        
                        <div class="mb-5" id="rejectNotesContainer" style="display: none;">
                            <label class="block text-sm sm:text-base font-black text-rose-600 mb-2">Catatan Penolakan (Wajib)</label>
                            <textarea name="notes" id="rejectNotes" class="w-full rounded-2xl border-2 border-rose-300 shadow-sm focus:border-rose-600 focus:ring focus:ring-rose-200 focus:ring-opacity-50 text-base p-4" rows="4" placeholder="Sebutkan alasan penolakan secara jelas agar guru dapat memperbaiki komitmennya..."></textarea>
                        </div>

                        <div class="flex flex-col gap-3.5">
                            <button type="button" class="w-full inline-flex justify-center items-center gap-3 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-5 py-3.5 rounded-2xl text-base font-black shadow-lg shadow-emerald-600/30 transition-all hover:-translate-y-0.5 border border-emerald-500" onclick="submitApprove()">
                                <i class="fas fa-check-circle text-lg"></i> Setujui Kontrak Ini
                            </button>
                            <button type="button" id="btnTolak" class="w-full inline-flex justify-center items-center gap-3 bg-rose-50 text-rose-700 border-2 border-rose-300 hover:bg-rose-100 px-5 py-3.5 rounded-2xl text-base font-black transition-all shadow-sm" onclick="showReject()">
                                <i class="fas fa-times-circle text-lg"></i> Tolak & Kembalikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: Dokumen Kontrak --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                <div class="bg-gradient-to-r from-slate-100 via-indigo-50/40 to-slate-100 border-b-2 border-gray-200 px-6 sm:px-8 py-6">
                    <h3 class="font-black text-gray-900 text-lg sm:text-xl flex items-center gap-3">
                        <i class="fas fa-file-contract text-indigo-600 text-xl"></i> Rincian Komitmen / Target Kinerja Riil
                    </h3>
                </div>
                <div class="p-6 sm:p-10">
                    
                    <div class="text-center mb-8 border-b-4 border-indigo-600 pb-6 inline-block w-full">
                        <h2 class="text-2xl sm:text-3xl font-black text-gray-900 uppercase tracking-wide leading-tight">
                            @if($contract->contract_type == 'pkg_kejuruan')
                                PERJANJIAN KINERJA GURU<br><span class="text-indigo-600">(PRODUKTIF/KEJURUAN)</span>
                            @elseif($contract->contract_type == 'pkg_umum')
                                PERJANJIAN KINERJA GURU<br><span class="text-indigo-600">(MAPEL UMUM)</span>
                            @else
                                PERJANJIAN KINERJA JABATAN
                            @endif
                        </h2>
                        <p class="text-base sm:text-lg font-black text-indigo-600 mt-2.5 uppercase tracking-widest bg-indigo-50 inline-block px-4 py-1.5 rounded-full border border-indigo-200">Tahun Pelajaran {{ $contract->academicYear->year }}</p>
                    </div>

                    <div class="bg-indigo-50/60 border-2 border-indigo-200/80 rounded-2xl p-5 sm:p-6 text-gray-800 leading-relaxed text-base sm:text-lg mb-8 shadow-inner font-medium">
                        <p>
                            Yang bertanda tangan di bawah ini, <strong class="text-indigo-950 font-black">Pihak yang menyatakan berjanji</strong> (<strong class="text-indigo-950 underline decoration-indigo-500 decoration-2">{{ $contract->employee->full_name }}</strong>), 
                            dengan ini menyatakan <strong class="text-indigo-950 font-black bg-amber-200/80 px-2 py-0.5 rounded">KOMITMEN DAN KESANGGUPAN PENUH</strong> untuk melaksanakan serta mencapai target kinerja riil pada Tahun Pelajaran {{ $contract->academicYear->year }}, sebagaimana tertuang secara rinci pada tabel bukti fisik nyata berikut ini:
                        </p>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border-2 border-gray-200 shadow-md">
                        @if(in_array($contract->contract_type, ['pkg_kejuruan', 'pkg_umum']))
                            <table class="w-full text-left border-collapse min-w-max">
                                <thead>
                                    <tr class="bg-gradient-to-r from-slate-100 via-gray-100 to-slate-100 border-b-2 border-gray-300">
                                        <th class="px-5 py-4 text-sm font-black text-gray-800 uppercase tracking-wider text-center border-r-2 border-gray-200" width="8%">No</th>
                                        <th class="px-5 py-4 text-sm font-black text-gray-800 uppercase tracking-wider border-r-2 border-gray-200" width="38%">Pilar Perjanjian Kinerja</th>
                                        <th class="px-5 py-4 text-sm font-black text-gray-800 uppercase tracking-wider">Rencana Bukti Fisik Nyata (Target)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">1</td>
                                        <td class="px-5 py-5 font-black text-base sm:text-lg text-indigo-950 border-r-2 border-gray-200 bg-gray-50/30">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kompetensi Praktik (30%)' : 'Kompetensi Relevansi Praktik (30%)' }}</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['pilar_1'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">2</td>
                                        <td class="px-5 py-5 font-black text-base sm:text-lg text-indigo-950 border-r-2 border-gray-200 bg-gray-50/30">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kontribusi Program (30%)' : 'Kontribusi Program/TEFA (30%)' }}</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['pilar_2'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">3</td>
                                        <td class="px-5 py-5 font-black text-base sm:text-lg text-indigo-950 border-r-2 border-gray-200 bg-gray-50/30">Kolaborasi (20%)</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['pilar_3'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">4</td>
                                        <td class="px-5 py-5 font-black text-base sm:text-lg text-indigo-950 border-r-2 border-gray-200 bg-gray-50/30">Budaya Industri 5R (20%)</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['pilar_4'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @elseif($contract->contract_type == 'jabatan_tambahan')
                            <table class="w-full text-left border-collapse min-w-max">
                                <thead>
                                    <tr class="bg-gradient-to-r from-slate-100 via-gray-100 to-slate-100 border-b-2 border-gray-300">
                                        <th class="px-5 py-4 text-sm font-black text-gray-800 uppercase tracking-wider text-center border-r-2 border-gray-200" width="8%">No</th>
                                        <th class="px-5 py-4 text-sm font-black text-gray-800 uppercase tracking-wider">Deskripsi Target Pekerjaan Riil</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-100">
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">1</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['target_1'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">2</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['target_2'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-5 py-5 text-center font-black text-base text-gray-800 border-r-2 border-gray-200 bg-gray-50/60">3</td>
                                        <td class="px-5 py-5 text-base sm:text-lg font-bold text-gray-800 leading-relaxed">{{ $contract->target_data['target_3'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function submitApprove() {
        document.getElementById('actionInput').value = 'approve';
        document.getElementById('rejectNotes').required = false;
        document.getElementById('approvalForm').submit();
    }

    function showReject() {
        const container = document.getElementById('rejectNotesContainer');
        const btnTolak = document.getElementById('btnTolak');
        
        if (container.style.display === 'none') {
            container.style.display = 'block';
            document.getElementById('rejectNotes').required = true;
            
            // Ubah tampilan tombol
            btnTolak.innerHTML = '<i class="fas fa-exclamation-triangle text-lg"></i> Konfirmasi Tolak & Kembalikan Kontrak';
            btnTolak.classList.remove('bg-rose-50', 'text-rose-700', 'border-rose-300');
            btnTolak.classList.add('bg-rose-600', 'text-white', 'border-rose-600', 'shadow-lg', 'shadow-rose-600/30');
        } else {
            // Eksekusi penolakan jika catatan sudah diisi
            if (document.getElementById('rejectNotes').value.trim() === '') {
                document.getElementById('rejectNotes').focus();
                return;
            }
            document.getElementById('actionInput').value = 'reject';
            document.getElementById('approvalForm').submit();
        }
    }
</script>
@endsection
