@extends('layouts.siswa')
@php
$isSMA = $student->school->type === 'SMA';
$entityName = $isSMA ? 'Penelitian Ilmiah' : 'Project Akhir';
@endphp
@section('title', $isSMA ? 'Pengajuan Penelitian Ilmiah' : 'Pengajuan Project Akhir')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="bg-white rounded-3xl border-2 border-gray-300 shadow-md p-6 md:p-8 space-y-3">
        <h1 class="text-xl md:text-2xl font-black text-gray-950 flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-md">
                <i class="fas fa-file-signature"></i>
            </div>
            Pengajuan Usulan {{ $entityName }}
        </h1>
        <p class="text-sm md:text-base text-gray-900 leading-relaxed font-bold pl-1">
            Siswa Kelas XII {{ $student->school->type === 'SMA' ? 'SMA Swasta Pembda Nias' : 'SMKS Swasta Pembda Nias' }} wajib menyusun {{ $student->school->type === 'SMA' ? 'Penelitian Ilmiah' : 'Project Akhir' }} sebagai syarat kelulusan akademis.
        </p>
    </div>

    {{-- Stepper Guideline Card --}}
    <div class="bg-white rounded-3xl border-2 border-gray-300 shadow-md p-6 md:p-8 space-y-6">
        <h2 class="text-base font-black text-gray-950 flex items-center gap-2 border-b-2 border-gray-200 pb-3">
            <i class="fas fa-project-diagram text-amber-700"></i> Tahapan & Alur Kegiatan {{ $entityName }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="flex flex-col items-start p-4 bg-amber-50 rounded-2xl border-2 border-amber-600 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-full bg-amber-600 text-white flex items-center justify-center font-black text-sm shadow">1</span>
                    <h3 class="font-black text-xs text-amber-950 uppercase tracking-wider">Pengajuan</h3>
                </div>
                <p class="text-xs text-gray-950 font-bold leading-relaxed">
                    Isi form judul, rencana penelitian/abstrak, dan pilih teman sekelas sebagai anggota kelompok (opsional). Kemudian kirim usulan.
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="flex flex-col items-start p-4 bg-blue-50 rounded-2xl border-2 border-blue-600 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow">2</span>
                    <h3 class="font-black text-xs text-blue-950 uppercase tracking-wider">Persetujuan</h3>
                </div>
                <p class="text-xs text-gray-950 font-bold leading-relaxed">
                    Admin sekolah akan meninjau judul Anda. Jika disetujui, Admin menetapkan Guru Pembimbing kelompok Anda.
                </p>
            </div>
            
            <!-- Step 3 -->
            <div class="flex flex-col items-start p-4 bg-emerald-50 rounded-2xl border-2 border-emerald-600 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow">3</span>
                    <h3 class="font-black text-xs text-emerald-950 uppercase tracking-wider">Bimbingan</h3>
                </div>
                <p class="text-xs text-gray-950 font-bold leading-relaxed">
                    Lakukan bimbingan berkala. Tulis dan catat progresnya di menu <strong class="text-emerald-900 font-extrabold">Logbook</strong> untuk mendapat poin reputasi (+10 poin).
                </p>
            </div>
            
            <!-- Step 4 -->
            <div class="flex flex-col items-start p-4 bg-purple-50 rounded-2xl border-2 border-purple-600 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-black text-sm shadow">4</span>
                    <h3 class="font-black text-xs text-purple-950 uppercase tracking-wider">Ujian Sidang</h3>
                </div>
                <p class="text-xs text-gray-950 font-bold leading-relaxed">
                    Setelah bimbingan selesai disetujui Guru Pembimbing, Admin menjadwalkan Ujian/Sidang Tugas Akhir kelompok Anda.
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-100 border-2 border-rose-400 text-rose-950 px-5 py-4 rounded-2xl text-xs md:text-sm shadow-md flex items-center gap-3">
            <i class="fas fa-circle-exclamation text-rose-700 text-lg"></i> 
            <span class="font-black">{{ session('error') }}</span>
        </div>
    @endif

    @if($student->school->type === 'SMK')
        <div class="bg-white rounded-3xl border-2 border-indigo-300 shadow-md p-8 md:p-12 text-center space-y-4">
            <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto text-4xl shadow-sm">
                <i class="fas fa-users-cog"></i>
            </div>
            <h2 class="text-xl font-black text-gray-950">Menunggu Penetapan Panitia</h2>
            <p class="text-sm font-bold text-gray-700 max-w-2xl mx-auto leading-relaxed">
                Untuk Project Akhir SMK, <strong>Judul Project</strong> dan <strong>Kelompok</strong> Anda akan ditentukan dan dibentuk langsung oleh Panitia Project Akhir Sekolah. 
                <br><br>
                Silakan pantau terus portal ini secara berkala. Jika kelompok Anda telah dibentuk, halaman ini akan otomatis berubah menjadi halaman aktivitas Project Akhir Anda.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Form Card --}}
            <div class="md:col-span-2 bg-white rounded-3xl border-2 border-gray-300 shadow-md p-6 md:p-8 space-y-5">
                <h2 class="text-base font-black text-gray-950 flex items-center gap-2 border-b-2 border-gray-200 pb-3">
                    <i class="fas fa-edit text-amber-700"></i> Form Pengajuan Judul
                </h2>
                <form action="{{ route('siswa.final-project.propose') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-950 uppercase tracking-wider mb-1.5">Jenis Usulan</label>
                        <input type="text" value="{{ $student->school->type === 'SMA' ? 'Penelitian Ilmiah (SMA)' : 'Project Akhir (SMK)' }}" disabled class="w-full bg-gray-100 border border-gray-300 rounded-xl px-4 py-2.5 text-xs font-black text-gray-700 cursor-not-allowed">
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-black text-gray-950 uppercase tracking-wider mb-1.5">Judul yang Diusulkan</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="Contoh: {{ $student->school->type === 'SMA' ? 'Analisis Kandungan Kimia Sumber Air Panas Gunungsitoli' : 'Rancang Bangun Aplikasi Pengarsipan Surat Berbasis Laravel' }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-bold text-gray-950 transition">
                        @error('title')
                            <p class="text-xs text-rose-600 mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="abstract" class="block text-xs font-black text-gray-950 uppercase tracking-wider mb-1.5">Rencana Penelitian / Abstrak Singkat</label>
                        <textarea name="abstract" id="abstract" required rows="5" placeholder="Jelaskan secara singkat latar belakang, tujuan, dan metode dari penelitian/project ini..." class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-bold text-gray-950 transition">{{ old('abstract') }}</textarea>
                        @error('abstract')
                            <p class="text-xs text-rose-600 mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-black text-gray-950 uppercase tracking-wider">
                            Pilih Anggota Kelompok <span class="text-gray-600 font-bold capitalize normal-case text-[10px] ml-1">(Bisa dikosongkan jika individu)</span>
                        </label>
                        
                        <div class="bg-gray-50 border-2 border-gray-300 rounded-2xl p-4 max-h-60 overflow-y-auto space-y-2">
                            @forelse($classmates as $cm)
                                <label class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-500 hover:shadow-sm transition-all group {{ is_array(old('member_ids')) && in_array($cm->id, old('member_ids')) ? 'border-amber-500 bg-amber-50' : '' }}">
                                    <div class="flex-shrink-0 relative flex items-center justify-center">
                                        <input type="checkbox" name="member_ids[]" value="{{ $cm->id }}" 
                                            {{ is_array(old('member_ids')) && in_array($cm->id, old('member_ids')) ? 'checked' : '' }}
                                            class="w-5 h-5 text-amber-600 rounded border-2 border-gray-400 focus:ring-amber-600 cursor-pointer" 
                                            style="position: relative !important; margin: 0 !important; float: none !important; opacity: 1 !important; appearance: checkbox !important; -webkit-appearance: checkbox !important;">
                                    </div>
                                    <div class="flex-1 min-w-0 pl-2">
                                        <p class="text-sm font-black text-gray-950 truncate">{{ $cm->full_name }}</p>
                                        <p class="text-xs text-gray-950 font-bold mt-0.5">NISN: {{ $cm->nisn ?? '-' }}</p>
                                    </div>
                                </label>
                            @empty
                                <div class="p-4 bg-amber-50 border-2 border-amber-600 rounded-2xl text-amber-950 font-bold text-xs">
                                    <i class="fas fa-info-circle mr-1 text-amber-700"></i> Tidak ada teman sekelas yang tersedia (semua sudah tergabung di kelompok).
                                </div>
                            @endforelse
                        </div>
                        @error('member_ids')
                            <p class="text-xs text-rose-600 mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2 border-t-2 border-gray-200">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-extrabold px-6 py-3 rounded-xl text-xs shadow-md hover:shadow-lg transition-all transform active:scale-95 flex items-center gap-1.5">
                            <i class="fas fa-paper-plane"></i> Kirim Usulan Judul
                        </button>
                    </div>
                </form>
            </div>

            {{-- Formats & Guidelines Sidebar --}}
            <div class="bg-white rounded-3xl border-2 border-gray-300 shadow-md p-6 space-y-4 h-fit">
                <h2 class="text-sm font-black text-gray-950 flex items-center gap-2 border-b-2 border-gray-200 pb-3">
                    <i class="fas fa-file-pdf text-rose-600"></i> Panduan & Format {{ $entityName }}
                </h2>
                <p class="text-xs text-gray-950 leading-relaxed font-bold">
                    Unduh template dokumen, lembar persetujuan, dan panduan penulisan resmi di bawah ini untuk memulai penyusunan proposal Anda:
                </p>

                <div class="space-y-3 pt-1">
                    @forelse($formats as $f)
                        <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-2xl border-2 border-gray-300 hover:bg-amber-50 hover:border-amber-600 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-rose-600 flex items-center justify-center text-white flex-shrink-0 text-xs border-2 border-rose-800 shadow-sm">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-black text-gray-950 truncate" title="{{ $f->title }}">{{ $f->title }}</p>
                                @if($f->description)
                                    <p class="text-xs text-gray-900 mt-0.5 line-clamp-2 leading-relaxed font-bold">{{ $f->description }}</p>
                                @endif
                                <div class="pt-1.5">
                                    <a href="{{ route('siswa.final-project.download-format', $f->id) }}" class="inline-flex items-center gap-1 text-xs text-amber-800 font-black hover:underline">
                                        <i class="fas fa-download text-gray-700"></i> Unduh File
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 bg-amber-50 border-2 border-amber-600 rounded-2xl text-amber-950 font-bold text-xs">
                            <i class="fas fa-info-circle mr-1 text-amber-700"></i> Belum ada file panduan yang diupload oleh sekolah.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
