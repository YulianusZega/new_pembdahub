@extends('layouts.admin')

@section('title', 'Edit Survei')

@section('content')
<style>
    .form-input { transition: all 0.2s ease; }
    .form-input:focus { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.12); }
</style>

<div class="space-y-6">
    {{-- HERO HEADER --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl" style="background: linear-gradient(135deg, #1e1b4b 0%, #2e1065 40%, #5b21b6 70%, #7c3aed 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #fff, transparent); transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, #a78bfa, transparent); transform: translate(-30%, 30%);"></div>
        <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="relative px-8 py-9 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-sm border border-white/20 rounded-full text-xs font-bold text-white/90 uppercase tracking-wider">
                        <i class="fas fa-edit text-[10px]"></i> Edit Kuisioner
                    </span>
                    @php
                        $statusBadge = ['draft' => 'bg-sky-500/90 border-sky-400/50', 'active' => 'bg-emerald-500/90 border-emerald-400/50', 'closed' => 'bg-rose-500/90 border-rose-400/50'];
                        $badgeClass = $statusBadge[$survey->status] ?? 'bg-gray-500/90 border-gray-400/50';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $badgeClass }} border rounded-full text-xs font-bold text-white uppercase tracking-wider">
                        {{ $survey->status }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Edit Detail Survei
                </h1>
                <p class="text-white/75 text-sm leading-relaxed max-w-xl truncate" title="{{ $survey->title }}">
                    Sedang mengedit: <strong>{{ $survey->title }}</strong>
                </p>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-950 hover:bg-indigo-50 px-5 py-3 rounded-2xl font-extrabold text-sm transition shadow-lg shadow-black/15">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.surveys.update', $survey->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 md:gap-8">
            {{-- LEFT COLUMN: General Information (8/12) --}}
            <div class="xl:col-span-8 space-y-6 md:space-y-8">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden h-full flex flex-col">
                    {{-- Card Header --}}
                    <div class="px-8 py-5 border-b border-gray-50 flex items-center gap-3" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-file-alt text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-gray-800 text-sm md:text-base">Informasi Umum</h2>
                            <p class="text-[11px] md:text-xs text-gray-400 font-bold">Judul dan panduan pengisian kuisioner</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-6 md:space-y-8 flex-1">
                        {{-- Title --}}
                        <div class="space-y-2">
                            <label for="title" class="text-xs md:text-sm font-extrabold text-gray-600 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-heading text-indigo-500"></i> Judul Survei <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $survey->title) }}" required
                                   placeholder="Contoh: Survei Perjanjian Kinerja Guru oleh Siswa Semester Genap"
                                   class="form-input w-full px-5 py-4 border-2 {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl focus:outline-none focus:border-indigo-400 focus:bg-white text-lg font-bold text-gray-800 placeholder-gray-400">
                            @error('title')
                                <p class="text-red-500 text-sm font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="space-y-2 flex-1 flex flex-col">
                            <label for="description" class="text-xs md:text-sm font-extrabold text-gray-600 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-align-left text-indigo-500"></i> Deskripsi / Petunjuk Pengisian
                            </label>
                            <textarea id="description" name="description" rows="10"
                                      placeholder="Tuliskan tujuan survei atau panduan bagi responden..."
                                      class="form-input w-full px-5 py-4 border-2 {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-2xl focus:outline-none focus:border-indigo-400 focus:bg-white text-base md:text-lg font-medium text-gray-800 placeholder-gray-400 resize-none leading-relaxed flex-1">{{ old('description', $survey->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm font-semibold flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Settings (4/12) --}}
            <div class="xl:col-span-4 space-y-6 md:space-y-8">
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
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-users text-indigo-400"></i> Target Responden <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach([
                                    ['value' => 'siswa', 'label' => 'Siswa (Student)', 'icon' => 'fa-user-graduate', 'color' => 'text-amber-600', 'desc' => 'Diisi oleh peserta didik'],
                                    ['value' => 'guru', 'label' => 'Guru (Teacher)', 'icon' => 'fa-chalkboard-teacher', 'color' => 'text-purple-600', 'desc' => 'Diisi oleh tenaga pendidik'],
                                    ['value' => 'semua', 'label' => 'Semua Peran', 'icon' => 'fa-users', 'color' => 'text-teal-600', 'desc' => 'Dapat diisi siswa & guru'],
                                ] as $opt)
                                @php $isChecked = old('target_respondent', $survey->target_respondent) === $opt['value']; @endphp
                                <label class="flex items-center gap-3 border-2 {{ $isChecked ? 'border-indigo-400 bg-indigo-50/70' : 'border-gray-200 bg-gray-50' }} rounded-xl px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition-all duration-200">
                                    <input type="radio" name="target_respondent" value="{{ $opt['value'] }}" required
                                           class="sr-only" {{ $isChecked ? 'checked' : '' }}
                                           onchange="document.querySelectorAll('[name=target_respondent]').forEach(r => { let l = r.closest('label'); l.className = l.className.replace('border-indigo-400 bg-indigo-50/70','border-gray-200 bg-gray-50'); }); let l = this.closest('label'); l.className = l.className.replace('border-gray-200 bg-gray-50','border-indigo-400 bg-indigo-50/70');">
                                    <i class="fas {{ $opt['icon'] }} {{ $opt['color'] }} w-5 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-extrabold text-gray-800">{{ $opt['label'] }}</span>
                                        <span class="block text-[11px] text-gray-400 font-semibold">{{ $opt['desc'] }}</span>
                                    </div>
                                    <div class="w-4 h-4 rounded-full border-2 {{ $isChecked ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }} flex-shrink-0"></div>
                                </label>
                                @endforeach
                            </div>
                            @error('target_respondent')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-toggle-on text-indigo-400"></i> Status Survei <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach([
                                    ['value' => 'draft', 'label' => 'Draft', 'icon' => 'fa-file-alt', 'color' => 'text-sky-600', 'desc' => 'Konsep - belum aktif/tersedia'],
                                    ['value' => 'active', 'label' => 'Active', 'icon' => 'fa-play-circle', 'color' => 'text-emerald-600', 'desc' => 'Langsung aktif & bisa diisi'],
                                    ['value' => 'closed', 'label' => 'Closed', 'icon' => 'fa-lock', 'color' => 'text-rose-600', 'desc' => 'Ditutup - pengisian berakhir'],
                                ] as $opt)
                                @php $isChecked = old('status', $survey->status) === $opt['value']; @endphp
                                <label class="flex items-center gap-3 border-2 {{ $isChecked ? 'border-indigo-400 bg-indigo-50/70' : 'border-gray-200 bg-gray-50' }} rounded-xl px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition-all duration-200">
                                    <input type="radio" name="status" value="{{ $opt['value'] }}" required
                                           class="sr-only" {{ $isChecked ? 'checked' : '' }}
                                           onchange="document.querySelectorAll('[name=status]').forEach(r => { let l = r.closest('label'); l.className = l.className.replace('border-indigo-400 bg-indigo-50/70','border-gray-200 bg-gray-50'); }); let l = this.closest('label'); l.className = l.className.replace('border-gray-200 bg-gray-50','border-indigo-400 bg-indigo-50/70');">
                                    <i class="fas {{ $opt['icon'] }} {{ $opt['color'] }} w-5 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-extrabold text-gray-800">{{ $opt['label'] }}</span>
                                        <span class="block text-[11px] text-gray-400 font-semibold">{{ $opt['desc'] }}</span>
                                    </div>
                                    <div class="w-4 h-4 rounded-full border-2 {{ $isChecked ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }} flex-shrink-0"></div>
                                </label>
                                @endforeach
                            </div>
                            @error('status')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Waktu Buka & Tutup --}}
                        <div class="space-y-4 pt-2">
                            <div class="space-y-2">
                                <label for="start_date" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="far fa-calendar-plus text-indigo-400"></i> Waktu Buka
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $survey->start_date ? $survey->start_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="form-input w-full px-4 py-3 border-2 {{ $errors->has('start_date') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} rounded-xl focus:outline-none focus:border-indigo-400 focus:bg-white text-sm font-semibold text-gray-800">
                                @error('start_date')
                                    <p class="text-red-500 text-xs font-semibold flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="space-y-2">
                                <label for="end_date" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="far fa-calendar-times text-indigo-400"></i> Waktu Tutup
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $survey->end_date ? $survey->end_date->format('Y-m-d\TH:i') : '') }}" 
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
                        <div class="space-y-2 p-5 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50">
                            <label for="school_id" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-school text-indigo-400"></i> Unit Sekolah Penyelenggara
                                <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100 ml-1">Super Admin</span>
                            </label>
                            <select id="school_id" name="school_id"
                                    class="form-input w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-2xl focus:outline-none focus:border-indigo-400 transition text-sm text-gray-750 font-semibold" style="appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.25em; padding-right: 2.5rem;">
                                <option value="">Semua Unit Sekolah (Global)</option>
                                @foreach($schools as $sch)
                                    <option value="{{ $sch->id }}" {{ old('school_id', $survey->school_id) == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1"><i class="fas fa-info-circle text-indigo-400 mr-1 text-[9px]"></i>Kosongkan untuk survei global.</p>
                            @error('school_id')
                                <p class="text-red-500 text-xs font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex items-center justify-between">
            <a href="{{ route('admin.surveys.questions', $survey->id) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-extrabold text-xs uppercase tracking-wider transition">
                <i class="fas fa-list-ol text-xs"></i>
                <span>Kelola Pertanyaan</span>
                <i class="fas fa-chevron-right text-[8px] ml-0.5"></i>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.surveys.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-white font-extrabold rounded-xl text-xs uppercase tracking-wider shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-save text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
