@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-10">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pemeriksaan Kontrak Kinerja</h2>
            <p class="text-sm text-gray-500 mt-1">Evaluasi dokumen perjanjian kinerja yang diajukan oleh guru.</p>
        </div>
        <a href="{{ route('admin.performance_contracts.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Info Pegawai & Status --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-indigo-50/50 border-b border-indigo-100 px-5 py-4">
                    <h3 class="font-bold text-indigo-900 flex items-center gap-2">
                        <i class="fas fa-id-badge text-indigo-500"></i> Informasi Pegawai
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</div>
                        <div class="font-bold text-gray-900">{{ $contract->employee->full_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">NIP / NUPTK</div>
                        <div class="font-medium text-gray-700">{{ $contract->employee->nip ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Unit Sekolah</div>
                        <div class="font-medium text-gray-700">{{ $contract->school->name ?? '-' }}</div>
                    </div>
                    <hr class="border-gray-100">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipe Kontrak</div>
                        @if($contract->contract_type == 'pkg_kejuruan')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">Form 2A (Kejuruan)</span>
                        @elseif($contract->contract_type == 'pkg_umum')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Form 2B (Mapel Umum)</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Form 4 (Jabatan)</span>
                        @endif
                    </div>
                    @if($contract->contract_type == 'jabatan_tambahan')
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jabatan Tambahan</div>
                        <div class="font-medium text-gray-900">{{ $contract->position->position_name ?? '-' }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Saat Ini</div>
                        @if($contract->status == 'submitted_to_kepsek')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200"><i class="fas fa-clock text-[10px]"></i> Menunggu Kepsek</span>
                        @elseif($contract->status == 'approved_by_kepsek')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"><i class="fas fa-clock text-[10px]"></i> Menunggu Yayasan</span>
                        @elseif($contract->status == 'approved_by_yayasan')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> ACC Yayasan</span>
                        @elseif($contract->status == 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> Ditolak</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">{{ $contract->status }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Area Approval (Hanya muncul jika belum di-ACC oleh pihak terkait) --}}
            @php
                $user = auth()->user();
                $canApprove = false;
                
                if ($user->isSuperAdmin() && $contract->status == 'approved_by_kepsek') {
                    $canApprove = true; // Yayasan memproses setelah Kepsek
                } elseif (!$user->isSuperAdmin() && $contract->status == 'submitted_to_kepsek') {
                    $canApprove = true; // Kepsek memproses yang baru masuk
                }
            @endphp

            @if($canApprove)
            <div class="bg-white rounded-2xl shadow-sm border-2 border-indigo-200 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-gavel text-indigo-500"></i> Tindakan Persetujuan
                    </h3>
                    
                    <form action="{{ route('admin.performance_contracts.process', $contract->id) }}" method="POST" id="approvalForm">
                        @csrf
                        <input type="hidden" name="action" id="actionInput" value="">
                        
                        <div class="mb-4" id="rejectNotesContainer" style="display: none;">
                            <label class="block text-sm font-bold text-rose-600 mb-2">Catatan Penolakan (Wajib)</label>
                            <textarea name="notes" id="rejectNotes" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50 text-sm" rows="3" placeholder="Sebutkan alasan penolakan agar guru memperbaiki komitmennya..."></textarea>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button type="button" class="w-full inline-flex justify-center items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-emerald-500/20 transition-all hover:-translate-y-0.5" onclick="submitApprove()">
                                <i class="fas fa-check-circle"></i> Setujui Kontrak Ini
                            </button>
                            <button type="button" id="btnTolak" class="w-full inline-flex justify-center items-center gap-2 bg-white text-rose-600 border-2 border-rose-200 hover:bg-rose-50 px-4 py-2 rounded-xl text-sm font-bold transition-all" onclick="showReject()">
                                Tolak & Kembalikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: Dokumen Kontrak --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-5">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-contract text-gray-500"></i> Rincian Komitmen / Target Kinerja
                    </h3>
                </div>
                <div class="p-6 md:p-8">
                    
                    <div class="text-center mb-8 border-b-4 border-indigo-600 pb-6 inline-block w-full">
                        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-wide leading-tight">
                            @if($contract->contract_type == 'pkg_kejuruan')
                                PENILAIAN KINERJA GURU<br><span class="text-indigo-600">(PRODUKTIF/KEJURUAN)</span>
                            @elseif($contract->contract_type == 'pkg_umum')
                                PENILAIAN KINERJA GURU<br><span class="text-indigo-600">(MAPEL UMUM)</span>
                            @else
                                KONTRAK KINERJA JABATAN
                            @endif
                        </h2>
                        <p class="text-sm font-bold text-gray-500 mt-2 uppercase tracking-widest">Tahun Pelajaran {{ $contract->academicYear->year }}</p>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed text-justify mb-8">
                        <p>
                            Yang bertanda tangan di bawah ini, <strong>Pihak yang menyatakan berjanji</strong> ({{ $contract->employee->full_name }}), 
                            dengan ini menyatakan <strong>KOMITMEN DAN KESANGGUPAN PENUH</strong> untuk melaksanakan serta mencapai target kinerja riil pada Tahun Pelajaran {{ $contract->academicYear->year }}, sebagaimana tertuang secara rinci pada tabel bukti fisik nyata berikut ini:
                        </p>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        @if(in_array($contract->contract_type, ['pkg_kejuruan', 'pkg_umum']))
                            <table class="w-full text-left border-collapse min-w-max">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-r border-gray-200" width="5%">No</th>
                                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-gray-200" width="35%">Pilar Penilaian Kinerja</th>
                                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Rencana Bukti Fisik Nyata (Target)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">1</td>
                                        <td class="px-4 py-4 font-bold text-gray-800 border-r border-gray-200">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kompetensi Praktik (30%)' : 'Kompetensi Relevansi Praktik (30%)' }}</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['pilar_1'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">2</td>
                                        <td class="px-4 py-4 font-bold text-gray-800 border-r border-gray-200">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kontribusi Program (30%)' : 'Kontribusi Program/TEFA (30%)' }}</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['pilar_2'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">3</td>
                                        <td class="px-4 py-4 font-bold text-gray-800 border-r border-gray-200">Kolaborasi (20%)</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['pilar_3'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">4</td>
                                        <td class="px-4 py-4 font-bold text-gray-800 border-r border-gray-200">Budaya Industri 5R (20%)</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['pilar_4'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @elseif($contract->contract_type == 'jabatan_tambahan')
                            <table class="w-full text-left border-collapse min-w-max">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-r border-gray-200" width="5%">No</th>
                                        <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Target Pekerjaan Riil</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">1</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['target_1'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">2</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['target_2'] ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-4 text-center font-medium text-gray-500 border-r border-gray-200">3</td>
                                        <td class="px-4 py-4 text-gray-700">{{ $contract->target_data['target_3'] ?? '-' }}</td>
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
            btnTolak.innerText = "Konfirmasi Tolak Kontrak";
            btnTolak.classList.remove('bg-white', 'text-rose-600', 'border-rose-200');
            btnTolak.classList.add('bg-rose-600', 'text-white', 'border-rose-600', 'shadow-md', 'shadow-rose-500/20');
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
