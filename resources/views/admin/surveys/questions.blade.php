@extends('layouts.admin')

@section('title', 'Kelola Pertanyaan Survei')

@section('content')
<style>
    .question-card { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); animation: slideIn 0.3s ease both; }
    .question-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1); border-color: #e0e7ff; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delete-btn-wrap { opacity: 0; transition: all 0.2s ease; }
    .question-card:hover .delete-btn-wrap { opacity: 1; }
    .form-input { transition: all 0.2s ease; }
    .form-input:focus { box-shadow: 0 4px 12px rgba(99,102,241,0.12); }
    .type-selector label { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
    .type-selector label:hover { transform: translateY(-1px); }
    .sticky-panel { position: sticky; top: 1.5rem; }
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
                        <i class="fas fa-list-ol text-[10px]"></i> Pengaturan Kuisioner
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
                    Kelola Pertanyaan Survei
                </h1>
                <p class="text-white/75 text-sm leading-relaxed max-w-xl truncate" title="{{ $survey->title }}">
                    {{ $survey->title }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3 self-start md:self-auto flex-shrink-0">
                <a href="{{ route('admin.surveys.results', $survey->id) }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-2xl font-extrabold text-sm transition shadow-lg shadow-emerald-700/20">
                    <i class="fas fa-chart-line text-xs"></i>
                    <span>Analisis Hasil</span>
                </a>
                <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-950 hover:bg-indigo-50 px-5 py-3 rounded-2xl font-extrabold text-sm transition shadow-lg shadow-black/15">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 md:gap-8">

        {{-- LEFT: Question List (8/12) --}}
        <div class="xl:col-span-8 space-y-4 md:space-y-6">
            {{-- Summary Bar --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 md:p-6 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-folder-open text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[11px] md:text-xs font-extrabold text-gray-400 uppercase tracking-widest">Daftar Pertanyaan</p>
                        <p class="text-base md:text-lg font-black text-gray-800 mt-0.5">{{ count($questions) }} Butir Kuisioner</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $scaleCount = collect($questions)->where('type', 'scale')->count();
                        $textCount = collect($questions)->where('type', 'text')->count();
                    @endphp
                    <span class="px-3.5 py-1.5 bg-teal-50 text-teal-700 border border-teal-200 rounded-xl text-[11px] font-extrabold uppercase shadow-sm">
                        <i class="fas fa-star text-[9px] mr-1"></i>{{ $scaleCount }} Rating
                    </span>
                    <span class="px-3.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-[11px] font-extrabold uppercase shadow-sm">
                        <i class="fas fa-align-left text-[9px] mr-1"></i>{{ $textCount }} Teks
                    </span>
                </div>
            </div>

            {{-- Questions Card --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                @if(count($questions) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($questions as $index => $q)
                            @php
                                $scaleLabels = [
                                    'likert_5' => ['label' => 'Likert 5', 'desc' => 'Sangat Kurang s.d. Sangat Setuju', 'color' => 'text-teal-700', 'bg' => 'bg-teal-50', 'border' => 'border-teal-200'],
                                    'likert_4' => ['label' => 'Likert 4', 'desc' => 'Tanpa Pilihan Netral', 'color' => 'text-indigo-700', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200'],
                                    'competence_5' => ['label' => 'Kompetensi', 'desc' => 'Sangat Kurang s.d. Sangat Menguasai', 'color' => 'text-violet-750', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200'],
                                    'yes_no' => ['label' => 'Ya / Tidak', 'desc' => 'Pilihan biner Ya atau Tidak', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
                                ];
                                $label = $q->type === 'scale' ? ($scaleLabels[$q->scale_type] ?? ['label' => 'Rating Skala', 'color' => 'text-gray-700', 'bg' => 'bg-gray-100', 'border' => 'border-gray-200']) : ['label' => 'Teks Bebas', 'color' => 'text-blue-700', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200'];
                            @endphp
                            <div class="p-6 md:p-8 question-card relative group hover:bg-gray-50/50">
                                <div class="flex gap-4 md:gap-6">
                                    {{-- Number --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-black shadow-sm text-sm md:text-base">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0 pb-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg {{ $label['bg'] }} {{ $label['border'] }} border {{ $label['color'] }} text-xs md:text-sm font-extrabold uppercase tracking-wider shadow-sm">
                                                <i class="fas {{ $q->type === 'scale' ? 'fa-star' : 'fa-align-left' }} text-[10px]"></i>
                                                {{ $label['label'] }}
                                            </span>
                                            
                                            @if($survey->target_respondent === 'semua' && isset($q->target_guru))
                                                @php
                                                    $guruColors = ['kejuruan' => 'bg-rose-50 text-rose-700 border-rose-200', 'umum' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200'];
                                                    $gColor = $guruColors[$q->target_guru] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg {{ $gColor }} border text-xs md:text-sm font-extrabold uppercase tracking-wider shadow-sm">
                                                    <i class="fas fa-bullseye text-[10px]"></i>
                                                    Khusus Guru {{ ucfirst($q->target_guru) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-base md:text-lg text-gray-800 font-bold leading-relaxed break-words">
                                            {{ $q->question_text }}
                                        </p>
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex-shrink-0 delete-btn-wrap absolute right-6 top-6 md:static">
                                        <form action="{{ route('admin.surveys.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center shadow-sm transition">
                                                <i class="fas fa-trash-alt text-[11px] md:text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 md:p-16 flex flex-col items-center justify-center text-center">
                        <div class="w-24 h-24 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-200 mb-5 shadow-inner">
                            <i class="fas fa-clipboard-list text-4xl"></i>
                        </div>
                        <h4 class="text-lg md:text-xl font-extrabold text-gray-800 mb-2">Belum Ada Pertanyaan</h4>
                        <p class="text-sm text-gray-500 max-w-md">Survei ini masih kosong. Silakan tambahkan pertanyaan pertama Anda melalui form di sebelah kanan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Add Question Form (4/12) --}}
        <div class="xl:col-span-4">
            <div class="sticky-panel space-y-6">
                {{-- Info Card --}}
                <div class="bg-gradient-to-br from-indigo-900 to-indigo-850 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full"></div>
                    <div class="flex items-center gap-2 mb-3 relative z-10">
                        <i class="fas fa-user-tag text-white/80 text-sm"></i>
                        <span class="font-extrabold text-xs uppercase tracking-wider">Target Responden</span>
                    </div>
                    <div class="flex items-center gap-2.5 relative z-10">
                        @php
                            $tIcon = ['guru' => 'fa-chalkboard-teacher', 'siswa' => 'fa-user-graduate', 'semua' => 'fa-users'];
                        @endphp
                        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="fas {{ $tIcon[$survey->target_respondent] ?? 'fa-users' }} text-white text-sm"></i>
                        </div>
                        <span class="font-black text-lg uppercase tracking-wide">{{ $survey->target_respondent }}</span>
                    </div>
                    <p class="text-white/70 text-[10px] mt-3 font-semibold relative z-10">
                        Kuisioner ini akan dimuat secara dinamis saat target responden login ke dashboard mereka.
                    </p>
                </div>

                {{-- Add Question Form --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-850 text-xs uppercase tracking-wider">Tambah Pertanyaan</h3>
                            <p class="text-[10px] text-gray-400 font-bold mt-0.5">Isi Butir Pertanyaan Baru</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.surveys.questions.store', $survey->id) }}" method="POST" class="p-5 space-y-4" x-data="{ type: 'scale' }">
                        @csrf

                        {{-- Type Selector --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Tipe Pertanyaan</label>
                            <div class="type-selector grid grid-cols-2 gap-2">
                                <label :class="type === 'scale' ? 'border-indigo-400 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-gray-50 text-gray-600'"
                                       class="flex items-center gap-2 border-2 rounded-xl px-3 py-2.5 cursor-pointer">
                                    <input type="radio" x-model="type" value="scale" name="type" class="sr-only">
                                    <i class="fas fa-star text-xs w-4 text-center"></i>
                                    <div>
                                        <span class="block text-xs font-extrabold">Skala</span>
                                        <span class="block text-[9px] font-medium opacity-70">Rating/Skor</span>
                                    </div>
                                </label>
                                <label :class="type === 'text' ? 'border-blue-400 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-600'"
                                       class="flex items-center gap-2 border-2 rounded-xl px-3 py-2.5 cursor-pointer">
                                    <input type="radio" x-model="type" value="text" name="type" class="sr-only">
                                    <i class="fas fa-align-left text-xs w-4 text-center"></i>
                                    <div>
                                        <span class="block text-xs font-extrabold">Teks</span>
                                        <span class="block text-[9px] font-medium opacity-70">Jawaban bebas</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Scale Type --}}
                        <div class="space-y-2" x-show="type === 'scale'" x-transition>
                            <label for="scale_type" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Model Skala Penilaian</label>
                            <select id="scale_type" name="scale_type"
                                    class="w-full px-3.5 py-2.5 border-2 border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:border-indigo-400 focus:bg-white transition text-xs font-bold text-gray-700" style="appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.1em; padding-right: 2.25rem;">
                                <option value="likert_5">⭐ Likert 5 (Sangat Kurang s.d. Sangat Setuju)</option>
                                <option value="likert_4">⭐ Likert 4 (Tanpa Pilihan Netral/Ragu-Ragu)</option>
                                <option value="competence_5">🏆 Kompetensi (Sangat Kurang s.d. Sangat Menguasai)</option>
                                <option value="yes_no">✅ Ya / Tidak (Yes / No)</option>
                            </select>
                        </div>

                        {{-- Question Text --}}
                        <div class="space-y-2">
                            <label for="question_text" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Butir Pertanyaan <span class="text-red-400">*</span></label>
                            <textarea id="question_text" name="question_text" rows="4" required
                                      placeholder="Contoh: Seberapa baik kemampuan guru dalam menyampaikan materi pembelajaran secara interaktif?"
                                      class="form-input w-full px-3.5 py-3 border-2 border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:border-indigo-400 focus:bg-white transition text-xs text-gray-700 placeholder-gray-300 resize-none leading-relaxed font-semibold"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 text-white font-extrabold rounded-xl text-xs uppercase tracking-wider shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer" style="background: linear-gradient(135deg, #6366f1, #7c3aed);">
                            <i class="fas fa-plus-circle text-xs"></i>
                            <span>Tambahkan Pertanyaan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
