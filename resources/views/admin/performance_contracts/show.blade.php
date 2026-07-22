@extends('layouts.admin')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto pb-12">
    {{-- Header Banner --}}
    @php
        $isYayasanView = $isYayasanView ?? (auth()->user()->isSuperAdmin() || auth()->user()->isYayasan() || request()->routeIs('yayasan.*'));
        $routePrefix = $isYayasanView ? 'yayasan.' : 'admin.';
    @endphp
    <div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-950/20 border border-indigo-700/40 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 space-y-2">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-indigo-200 text-xs sm:text-sm font-bold uppercase tracking-wider">
                <i class="fas fa-file-contract text-amber-400"></i>
                <span>Verifikasi Dokumen Resmi</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Pemeriksaan Perjanjian Kinerja</h2>
            <p class="text-indigo-100/90 font-medium text-sm sm:text-base max-w-xl leading-relaxed">Evaluasi dan tinjau rincian target kinerja riil yang diajukan oleh guru atau tenaga kependidikan.</p>
        </div>
        
        <div class="relative z-10 shrink-0">
            <a href="{{ route($routePrefix . 'performance_contracts.index') }}" class="inline-flex items-center gap-2.5 bg-white hover:bg-indigo-50 text-indigo-950 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 border-2 border-white">
                <i class="fas fa-arrow-left text-indigo-600"></i> Kembali ke Daftar
            </a>
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
                    <div>
                        <div class="text-xs font-black text-indigo-500 uppercase tracking-wider mb-2">Status Saat Ini</div>
                        @if($contract->status == 'submitted_to_kepsek')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-400 shadow-sm w-full">
                                <i class="fas fa-clock text-amber-600 animate-pulse"></i> Menunggu Kepsek
                            </span>
                        @elseif($contract->status == 'approved_by_kepsek')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-400 shadow-sm w-full">
                                <i class="fas fa-spinner text-blue-600 animate-spin"></i> Menunggu Yayasan
                            </span>
                        @elseif($contract->status == 'approved_by_yayasan')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-black bg-emerald-100 text-emerald-900 border-2 border-emerald-400 shadow-sm w-full">
                                <i class="fas fa-check-circle text-emerald-600"></i> ACC Yayasan
                            </span>
                        @elseif($contract->status == 'rejected')
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-black bg-rose-100 text-rose-900 border-2 border-rose-400 shadow-sm w-full">
                                <i class="fas fa-times-circle text-rose-600"></i> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 border border-gray-300 w-full">{{ $contract->status }}</span>
                        @endif
                    </div>
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
