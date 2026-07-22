@extends('layouts.admin')

@section('title', 'Buat Kelompok Project Akhir')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Buat Kelompok Project Akhir</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Bentuk kelompok dan tentukan judul Project Akhir untuk siswa (khususnya SMK).</p>
    </div>
    <a href="{{ route('admin.final-projects.proposals.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
        <form method="GET" action="{{ route('admin.final-projects.proposals.create') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-1/3">
                <label for="classroom_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kelas</label>
                <select name="classroom_id" id="classroom_id" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas XII --</option>
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->school->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('classroom_id'))
                <div class="text-xs font-medium text-slate-500 pb-2">
                    Menampilkan data siswa untuk kelas yang dipilih.
                </div>
            @endif
        </form>
    </div>

    @if($selectedClassroom)
        <div class="p-6">
            <form action="{{ route('admin.final-projects.proposals.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Kiri: Detail Project -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2">Informasi Project</h3>
                        
                        <div>
                            <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul Project Akhir <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="Masukkan judul project akhir..." class="w-full rounded-xl border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="abstract" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi / Abstrak Singkat <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="abstract" id="abstract" rows="4" placeholder="Penjelasan singkat mengenai project ini..." class="w-full rounded-xl border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('abstract') }}</textarea>
                            @error('abstract') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="advisor_id" class="block text-sm font-bold text-slate-700 mb-2">Guru Pembimbing <span class="text-rose-500">*</span></label>
                            <select name="advisor_id" id="advisor_id" required class="w-full rounded-xl border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Pilih Guru Pembimbing --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('advisor_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                        @if($isSA) ({{ $teacher->school->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('advisor_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kanan: Pilih Anggota -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Pilih Anggota Kelompok</h3>
                        
                        @if($students->isEmpty())
                            <div class="bg-amber-50 text-amber-800 p-4 rounded-xl border border-amber-200 text-sm font-medium">
                                <i class="fas fa-exclamation-circle mr-1"></i> Semua siswa di kelas ini sudah memiliki kelompok Project Akhir.
                            </div>
                        @else
                            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 max-h-[400px] overflow-y-auto">
                                <p class="text-xs text-slate-500 font-medium mb-3">Centang siswa yang akan dimasukkan ke kelompok ini. Siswa yang dicentang pertama kali akan dianggap sebagai Ketua.</p>
                                
                                <div class="space-y-2">
                                    @foreach($students as $student)
                                        <label class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                            <input type="checkbox" name="member_ids[]" value="{{ $student->id }}" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500" {{ is_array(old('member_ids')) && in_array($student->id, old('member_ids')) ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <span class="block text-sm font-bold text-slate-800">{{ $student->full_name }}</span>
                                                <span class="block text-xs text-slate-500">NISN: {{ $student->nisn ?? '-' }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('member_ids') <p class="mt-2 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition-all flex items-center gap-2" {{ $students->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> Bentuk Kelompok & Simpan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Kelas Dipilih</h3>
            <p class="text-slate-500 text-sm">Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa dan membentuk kelompok.</p>
        </div>
    @endif
</div>
@endsection
