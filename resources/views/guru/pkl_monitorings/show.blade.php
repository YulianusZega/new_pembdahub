@extends('layouts.guru')

@section('title', 'Detail Monitoring PKL')
@section('page_title', 'Detail Monitoring PKL - ' . ($dudi->name ?? 'DUDI'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">{{ $dudi->name ?? 'Unknown DUDI' }}</h2>
            <p class="text-sm text-slate-500 mt-1">Shift: {{ $shift ?: 'Tidak ada shift' }}</p>
        </div>
        <a href="{{ route('guru.pkl_monitorings.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Siswa & Logbook Preview -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-users text-indigo-500 mr-2"></i> Daftar Siswa Bimbingan</h3>
                </div>
                <div class="p-0">
                    @foreach($placements as $placement)
                        <div class="p-5 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $placement->student->user->name ?? 'Nama Siswa' }}</h4>
                                    <p class="text-xs text-slate-500">{{ $placement->student->nisn ?? '-' }} &bull; {{ $placement->student->classroom->class_name ?? '-' }}</p>
                                </div>
                                <a href="{{ route('guru.pkl.show', $placement->id) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition-colors" target="_blank">
                                    <i class="fas fa-book-open mr-1"></i> Buka Logbook Lengkap
                                </a>
                            </div>
                            
                            @if($placement->logs->isNotEmpty())
                                <div class="mt-4 p-3 bg-white rounded-xl border border-slate-200">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Aktivitas Terakhir:</p>
                                    <ul class="space-y-2">
                                        @foreach($placement->logs as $log)
                                            <li class="flex items-start gap-2 text-sm">
                                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 text-xs"></i>
                                                <div>
                                                    <span class="font-medium text-slate-700">{{ $log->log_date->format('d/m/Y') }}:</span>
                                                    <span class="text-slate-600">{{ Str::limit($log->activity, 80) }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 border-dashed text-center">
                                    <p class="text-xs text-slate-500">Siswa belum mengisi logbook.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- History Monitoring -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-history text-indigo-500 mr-2"></i> Riwayat Laporan Monitoring</h3>
                </div>
                <div class="p-0">
                    @forelse($monitorings as $mon)
                        <div class="p-5 border-b border-slate-100 last:border-0 flex flex-col sm:flex-row justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $mon->monitoring_date->format('d F Y') }}</h4>
                                <p class="text-sm text-slate-600 mt-1">{{ $mon->notes ?? 'Tidak ada catatan' }}</p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                @if($mon->photo_path)
                                    <a href="{{ Storage::url($mon->photo_path) }}" target="_blank" class="w-10 h-10 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors" title="Lihat Foto">
                                        <i class="fas fa-image"></i>
                                    </a>
                                @endif
                                @if($mon->assignment_letter_path)
                                    <a href="{{ Storage::url($mon->assignment_letter_path) }}" target="_blank" class="w-10 h-10 flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl transition-colors" title="Lihat Surat">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">
                            Belum ada riwayat pelaporan monitoring untuk DUDI ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Form Laporan & Perangkat -->
        <div class="space-y-6">
            <!-- Form Perangkat -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-clipboard-check text-indigo-500 mr-2"></i> Kesiapan Perangkat</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('guru.pkl_monitorings.update-perangkat', [$dudi->id, $shift ?: 'null']) }}" method="POST">
                        @csrf
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center mt-0.5">
                                <input type="checkbox" name="is_perangkat_ready" class="peer appearance-none w-5 h-5 border-2 border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 checked:bg-indigo-600 checked:border-indigo-600 transition-all cursor-pointer" {{ $isPerangkatReady ? 'checked' : '' }} onchange="this.form.submit()">
                                <i class="fas fa-check absolute text-white text-xs opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Perangkat PKL Siap</span>
                                <p class="text-xs text-slate-500 mt-1">Centang jika dokumen monitoring (buku penilaian, absen, dll) sudah disiapkan oleh Panitia untuk DUDI ini.</p>
                            </div>
                        </label>
                    </form>
                </div>
            </div>

            <!-- Form Buat Laporan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-pen-alt text-indigo-500 mr-2"></i> Buat Laporan Mingguan</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('guru.pkl_monitorings.store', [$dudi->id, $shift ?: 'null']) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Tanggal Monitoring <span class="text-rose-500">*</span></label>
                            <input type="date" name="monitoring_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Upload Surat Monitoring (PDF/IMG) <span class="text-rose-500">*</span></label>
                            <input type="file" name="assignment_letter" required accept=".pdf,image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-[11px] text-slate-500 mt-1">Surat yang sudah ditandatangani oleh pihak DUDI.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Foto Bukti Kunjungan <span class="text-rose-500">*</span></label>
                            <input type="file" name="photo" required accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Catatan Khusus (Opsional)</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400" placeholder="Ketik catatan kondisi PKL siswa di sini..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-200 transition-all">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
