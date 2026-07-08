@extends('layouts.admin')

@section('title', 'Buat Survei Baru')

@section('content')
<style>
    .form-group { transition: all 0.2s ease; }
    .form-input { transition: all 0.2s ease; }
    .form-input:focus { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.12); }
    .step-indicator { transition: all 0.3s ease; }
    .radio-card { transition: all 0.2s ease; cursor: pointer; }
    .radio-card:hover { transform: translateY(-1px); }
    .radio-card input[type="radio"]:checked + .radio-inner { border-color: #6366f1; background: linear-gradient(135deg, #eef2ff, #e0e7ff); }
</style>

<div class="space-y-6">
    {{-- BREADCRUMB HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-2">
                <a href="{{ route('admin.surveys.index') }}" class="hover:text-indigo-600 transition font-semibold">Survey</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-gray-600 font-bold">Buat Baru</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-gray-800">Buat Survei Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">Mulai membuat kuisioner evaluasi kepuasan baru untuk responden sekolah.</p>
        </div>
        <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl font-bold text-sm transition shadow-sm hover:shadow">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('admin.surveys.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- LEFT COLUMN: General Information (3/5) --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden h-full flex flex-col">
                    {{-- Card Header --}}
                    <div class="px-8 py-5 border-b border-gray-50 flex items-center gap-3" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-file-alt text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-gray-800 text-sm">Informasi Umum</h2>
                            <p class="text-[11px] text-gray-400 font-bold">Judul dan panduan pengisian kuisioner</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-6 flex-1">
                        {{-- Title --}}
                        <div class="form-group space-y-2">
                            <label for="title" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-heading text-indigo-400"></i> Judul Survei <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                   placeholder="Contoh: Survei Penilaian Kinerja Guru oleh Siswa Semester Genap"
                                   class="form-input w-full px-4 py-3 border-2 {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl focus:outline-none focus:border-indigo-400 focus:bg-white text-sm font-semibold text-gray-800 placeholder-gray-300">
                            @error('title')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="form-group space-y-2 flex-1 flex flex-col">
                            <label for="description" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-align-left text-indigo-400"></i> Deskripsi / Petunjuk Pengisian
                            </label>
                            <textarea id="description" name="description" rows="8"
                                      placeholder="Tuliskan tujuan survei atau panduan bagi responden..."
                                      class="form-input w-full px-4 py-3 border-2 {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl focus:outline-none focus:border-indigo-400 focus:bg-white text-sm font-semibold text-gray-800 placeholder-gray-300 resize-none leading-relaxed flex-1">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Settings (2/5) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Card Header --}}
                    <div class="px-8 py-5 border-b border-gray-50 flex items-center gap-3" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-sliders-h text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-gray-800 text-sm">Pengaturan Survei</h2>
                            <p class="text-[11px] text-gray-400 font-bold">Sasaran dan status keaktifan</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        {{-- Target Respondent --}}
                        <div class="form-group space-y-2">
                            <label for="target_respondent" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-users text-indigo-400"></i> Target Responden <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach([
                                    ['value' => 'siswa', 'label' => 'Siswa (Student)', 'icon' => 'fa-user-graduate', 'color' => 'text-amber-600', 'desc' => 'Diisi oleh peserta didik'],
                                    ['value' => 'guru', 'label' => 'Guru (Teacher)', 'icon' => 'fa-chalkboard-teacher', 'color' => 'text-purple-600', 'desc' => 'Diisi oleh tenaga pendidik'],
                                    ['value' => 'semua', 'label' => 'Semua Peran', 'icon' => 'fa-users', 'color' => 'text-teal-600', 'desc' => 'Dapat diisi siswa & guru'],
                                ] as $opt)
                                <label class="radio-card flex items-center gap-3 border-2 {{ old('target_respondent') === $opt['value'] ? 'border-indigo-400 bg-indigo-50/70' : 'border-gray-200 bg-gray-50' }} rounded-xl px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition-all duration-200">
                                    <input type="radio" name="target_respondent" value="{{ $opt['value'] }}" required
                                           class="sr-only" {{ old('target_respondent') === $opt['value'] ? 'checked' : '' }}
                                           onchange="document.querySelectorAll('[name=target_respondent]').forEach(r => { let l = r.closest('label'); l.className = l.className.replace('border-indigo-400 bg-indigo-50/70','border-gray-200 bg-gray-50'); }); let l = this.closest('label'); l.className = l.className.replace('border-gray-200 bg-gray-50','border-indigo-400 bg-indigo-50/70');">
                                    <i class="fas {{ $opt['icon'] }} {{ $opt['color'] }} w-5 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-extrabold text-gray-800">{{ $opt['label'] }}</span>
                                        <span class="block text-[11px] text-gray-400 font-semibold">{{ $opt['desc'] }}</span>
                                    </div>
                                    <div class="w-4 h-4 rounded-full border-2 {{ old('target_respondent') === $opt['value'] ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }} flex-shrink-0"></div>
                                </label>
                                @endforeach
                            </div>
                            @error('target_respondent')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="form-group space-y-2">
                            <label for="status" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-toggle-on text-indigo-400"></i> Status Awal <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach([
                                    ['value' => 'draft', 'label' => 'Draft', 'icon' => 'fa-file-alt', 'color' => 'text-sky-600', 'desc' => 'Konsep - belum aktif/tersedia'],
                                    ['value' => 'active', 'label' => 'Active', 'icon' => 'fa-play-circle', 'color' => 'text-emerald-600', 'desc' => 'Langsung aktif & bisa diisi'],
                                    ['value' => 'closed', 'label' => 'Closed', 'icon' => 'fa-lock', 'color' => 'text-rose-600', 'desc' => 'Ditutup - pengisian berakhir'],
                                ] as $opt)
                                @php $isDefault = old('status', 'draft') === $opt['value']; @endphp
                                <label class="radio-card flex items-center gap-3 border-2 {{ $isDefault ? 'border-indigo-400 bg-indigo-50/70' : 'border-gray-200 bg-gray-50' }} rounded-xl px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition-all duration-200">
                                    <input type="radio" name="status" value="{{ $opt['value'] }}" required
                                           class="sr-only" {{ $isDefault ? 'checked' : '' }}
                                           onchange="document.querySelectorAll('[name=status]').forEach(r => { let l = r.closest('label'); l.className = l.className.replace('border-indigo-400 bg-indigo-50/70','border-gray-200 bg-gray-50'); }); let l = this.closest('label'); l.className = l.className.replace('border-gray-200 bg-gray-50','border-indigo-400 bg-indigo-50/70');">
                                    <i class="fas {{ $opt['icon'] }} {{ $opt['color'] }} w-5 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-extrabold text-gray-800">{{ $opt['label'] }}</span>
                                        <span class="block text-[11px] text-gray-400 font-semibold">{{ $opt['desc'] }}</span>
                                    </div>
                                    <div class="w-4 h-4 rounded-full border-2 {{ $isDefault ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }} flex-shrink-0"></div>
                                </label>
                                @endforeach
                            </div>
                            @error('status')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Waktu Buka & Tutup --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group space-y-2">
                                <label for="start_date" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="far fa-calendar-plus text-indigo-400"></i> Waktu Buka <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                       class="form-input w-full px-4 py-3 border-2 {{ $errors->has('start_date') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-xl focus:outline-none focus:border-indigo-400 focus:bg-white text-sm font-semibold text-gray-800">
                                @error('start_date')
                                    <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="form-group space-y-2">
                                <label for="end_date" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="far fa-calendar-times text-indigo-400"></i> Waktu Tutup <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                       class="form-input w-full px-4 py-3 border-2 {{ $errors->has('end_date') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-xl focus:outline-none focus:border-indigo-400 focus:bg-white text-sm font-semibold text-gray-800">
                                @error('end_date')
                                    <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- School (SuperAdmin Only) --}}
                        @if(auth()->user()->isSuperAdmin())
                        <div class="form-group space-y-2 p-5 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50">
                            <label for="school_id" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-school text-indigo-400"></i> Unit Sekolah Penyelenggara
                                <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100 ml-1">Super Admin</span>
                            </label>
                            <select id="school_id" name="school_id"
                                    class="form-input w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-2xl focus:outline-none focus:border-indigo-400 transition text-sm text-gray-750 font-semibold" style="appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.25em; padding-right: 2.5rem;">
                                <option value="">Semua Unit Sekolah (Global)</option>
                                @foreach($schools as $sch)
                                    <option value="{{ $sch->id }}" {{ old('school_id') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">
                                <i class="fas fa-info-circle text-indigo-400 mr-1 text-[9px]"></i>Kosongkan untuk survei global.
                            </p>
                            @error('school_id')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.surveys.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-white font-extrabold rounded-xl text-sm shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5" style="background: linear-gradient(135deg, #6366f1, #7c3aed);">
                <i class="fas fa-save text-xs"></i>
                <span>Simpan & Lanjutkan</span>
            </button>
        </div>
    </form>
</div>
@endsection
