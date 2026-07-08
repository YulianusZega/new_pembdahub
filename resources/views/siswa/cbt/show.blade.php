@extends('layouts.siswa')
@section('title', $exam->exam_title)
@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-orange-500 to-rose-600 rounded-2xl p-8 text-white">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative flex items-center gap-5">
            <a href="{{ route('siswa.cbt.index') }}" class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center hover:bg-white/25 transition border border-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $exam->exam_title }}</h1>
                <p class="text-amber-200 mt-1 text-sm">{{ $exam->subject->subject_name ?? $exam->subject->name ?? '-' }} &bull; {{ strtoupper($exam->exam_type) }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-times text-red-600 text-sm"></i></div>
        <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Exam Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-info-circle text-amber-600"></i></div>
            <h2 class="text-lg font-bold text-gray-900">Informasi Ujian</h2>
        </div>
        @if($exam->exam_description)
        <p class="text-gray-700 mb-4 text-sm leading-relaxed">{{ $exam->exam_description }}</p>
        @endif
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @php $infoItems = [
                ['Jumlah Soal', $exam->total_questions_shown, 'fa-list-ol', 'blue'],
                ['Durasi', $exam->duration_minutes . ' menit', 'fa-clock', 'emerald'],
                ['KKM', $exam->passing_score, 'fa-bullseye', 'red'],
                ['Percobaan', $attemptsUsed . '/' . $exam->max_attempts, 'fa-redo', 'purple'],
            ]; @endphp
            @foreach($infoItems as [$label, $val, $icon, $color])
            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas {{ $icon }} text-{{ $color }}-500 text-xs"></i>
                    <span class="text-xs text-gray-500 font-medium">{{ $label }}</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $val }}</span>
            </div>
            @endforeach
            @if($exam->start_time)
            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-2 mb-1"><i class="fas fa-calendar-check text-green-500 text-xs"></i><span class="text-xs text-gray-500 font-medium">Mulai</span></div>
                <span class="text-sm font-bold text-gray-900">{{ $exam->start_time->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            @if($exam->end_time)
            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-2 mb-1"><i class="fas fa-calendar-times text-red-500 text-xs"></i><span class="text-xs text-gray-500 font-medium">Berakhir</span></div>
                <span class="text-sm font-bold text-gray-900">{{ $exam->end_time->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Peraturan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-red-600"></i></div>
            <h2 class="text-lg font-bold text-gray-900">Peraturan Ujian</h2>
        </div>
        <div class="space-y-3">
            <div class="flex items-start gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                <span class="text-sm text-gray-700">Pastikan koneksi internet stabil sebelum memulai ujian.</span>
            </div>
            <div class="flex items-start gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                <span class="text-sm text-gray-700">Jawaban otomatis tersimpan setiap kali Anda memilih atau mengisi jawaban.</span>
            </div>
            @if($exam->prevent_tab_switch)
            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                <span class="text-sm text-gray-700"><strong>Jangan berpindah tab!</strong> Sistem akan mendeteksi dan ujian bisa dikumpulkan otomatis.</span>
            </div>
            @endif
            @if($exam->prevent_copy_paste)
            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                <i class="fas fa-ban text-red-500 mt-0.5"></i>
                <span class="text-sm text-gray-700">Copy-paste diblokir selama ujian berlangsung.</span>
            </div>
            @endif
            <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                <i class="fas fa-clock text-amber-500 mt-0.5"></i>
                <span class="text-sm text-gray-700">Waktu berjalan otomatis. Ujian dikumpulkan saat waktu habis.</span>
            </div>
            @if($exam->randomize_questions)
            <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <i class="fas fa-random text-blue-500 mt-0.5"></i>
                <span class="text-sm text-gray-700">Urutan soal diacak untuk setiap peserta.</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Last Result --}}
    @if($lastResult)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center"><i class="fas fa-chart-line text-blue-600"></i></div>
            <h2 class="text-lg font-bold text-gray-900">Hasil Terakhir</h2>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-center px-6">
                <div class="w-20 h-20 rounded-2xl mx-auto flex items-center justify-center {{ $lastResult->final_score >= $exam->passing_score ? 'bg-emerald-100' : 'bg-red-100' }}">
                    <span class="text-3xl font-bold {{ $lastResult->final_score >= $exam->passing_score ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($lastResult->final_score, 1) }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-2">Nilai</div>
            </div>
            <div class="flex-1 grid grid-cols-3 gap-4 text-center">
                <div class="p-3 bg-emerald-50 rounded-xl">
                    <div class="text-xl font-bold text-emerald-600">{{ $lastResult->correct_answers }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Benar</div>
                </div>
                <div class="p-3 bg-red-50 rounded-xl">
                    <div class="text-xl font-bold text-red-600">{{ $lastResult->wrong_answers }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Salah</div>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <div class="text-xl font-bold text-gray-600">{{ $lastResult->unanswered }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Kosong</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Action --}}
    <div class="text-center py-4">
        @if($activeSession)
        <form action="{{ route('siswa.cbt.start', $exam) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition text-lg font-bold gap-3">
                <i class="fas fa-redo"></i>Lanjutkan Ujian
            </button>
        </form>
        @elseif($attemptsUsed < $exam->max_attempts)
            @if($exam->access_code)
            <form action="{{ route('siswa.cbt.verify-access', $exam) }}" method="POST" class="inline-block">
                @csrf
                <div class="flex items-center gap-3 justify-center">
                    <input type="text" name="access_code" placeholder="Masukkan kode akses"
                        class="rounded-xl border-gray-200 shadow-sm focus:ring-amber-500 focus:border-amber-500 text-center px-5 py-3" required>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:shadow-lg transition font-bold flex items-center gap-2">
                        <i class="fas fa-key"></i>Verifikasi & Mulai
                    </button>
                </div>
            </form>
            @else
            <form action="{{ route('siswa.cbt.start', $exam) }}" method="POST" class="inline-block"
                onsubmit="return confirm('Yakin ingin memulai ujian? Timer akan langsung berjalan.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:shadow-lg transition text-lg font-bold gap-3">
                    <i class="fas fa-play"></i>Mulai Ujian
                </button>
            </form>
            @endif
        @else
        <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
            <i class="fas fa-ban text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 font-medium">Semua percobaan telah digunakan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
