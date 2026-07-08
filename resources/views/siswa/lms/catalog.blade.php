@extends('layouts.siswa')

@section('title', 'Katalog Course - LMS')

@push('styles')
<style>
    .course-card { animation: fadeUp 0.4s ease both; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="space-y-8" x-data="{ searchQuery: '' }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('siswa.lms.index') }}" class="hover:text-indigo-600 transition-colors">LMS</a>
                <i class="fas fa-chevron-right text-[8px] text-gray-400"></i>
                <span class="text-gray-700">Katalog Course</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Katalog Course</h1>
            <p class="text-sm text-gray-500 mt-1">Temukan dan daftarkan diri Anda ke course/kelas baru yang tersedia.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 text-xs">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Cari nama course atau mapel..." class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white shadow-sm">
            </div>
            <a href="{{ route('siswa.lms.index') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left"></i> Course Saya
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-rose-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Course Grid --}}
    @if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        @php
            $colorConfig = \App\Models\LmsCourse::getColorClasses($course->color);
            $scientist = $course->getScientistConfig();
            $subjectName = $course->subject->subject_name ?? 'Mata Pelajaran';
            $courseName = $course->course_name ?? $course->name;
        @endphp
        <div x-show="searchQuery === '' || '{{ strtolower($courseName) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($subjectName) }}'.includes(searchQuery.toLowerCase())"
             class="course-card group">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col">
                {{-- Card Header --}}
                <div class="relative bg-gradient-to-br {{ $colorConfig['gradient'] }} p-5 overflow-hidden">
                    <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 70% 30%, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        @if($scientist)
                        <svg class="w-28 h-28 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $scientist['icon'] !!}</svg>
                        @else
                        <i class="fas fa-graduation-cap text-7xl text-white"></i>
                        @endif
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center border border-gray-100 flex-shrink-0 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                @if($scientist)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $scientist['icon'] !!}</svg>
                                @else
                                <i class="fas fa-graduation-cap text-white"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-white text-base leading-snug line-clamp-2">{{ $courseName }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="bg-white/20 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest">{{ $course->getShortCode() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Teacher --}}
                        <div class="flex items-center gap-2 text-white/80 text-xs">
                            <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                            <span class="font-medium truncate">{{ $course->teacher->user->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        {{-- Course Description --}}
                        <p class="text-gray-500 text-sm mb-4 line-clamp-3 leading-relaxed">{{ $course->description ?: 'Belajar ' . $subjectName . ' secara interaktif bersama guru dan teman-teman.' }}</p>

                        {{-- Mini Stats --}}
                        <div class="flex items-center gap-3 text-[11px] text-gray-400 font-medium mb-4 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                            <span class="flex items-center gap-1"><i class="fas fa-file-alt text-blue-400"></i> {{ $course->materials_count }} Materi</span>
                            <span class="text-gray-200">·</span>
                            <span class="flex items-center gap-1"><i class="fas fa-tasks text-emerald-400"></i> {{ $course->assignments_count }} Tugas</span>
                            <span class="text-gray-200">·</span>
                            <span class="flex items-center gap-1"><i class="fas fa-question-circle text-purple-400"></i> {{ $course->quizzes_count }} Quiz</span>
                        </div>
                    </div>

                    {{-- CTA Enroll Button --}}
                    <div class="mt-4">
                        <form action="{{ route('siswa.lms.enroll', $course->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin mendaftar ke course {{ $courseName }}?')">
                            @csrf
                            <button type="submit" class="flex items-center justify-center gap-2 w-full {{ $colorConfig['bg'] }} hover:brightness-110 text-white px-4 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-bold group-hover:-translate-y-0.5 active:translate-y-0">
                                <i class="fas fa-sign-in-alt"></i> Daftar Course Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="pt-4">{{ $courses->links() }}</div>
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i class="fas fa-compass text-4xl text-indigo-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Katalog Kosong</h3>
        <p class="text-gray-500 max-w-sm mx-auto leading-relaxed mb-6">Tidak ada course baru yang tersedia untuk didaftarkan saat ini.</p>
        <a href="{{ route('siswa.lms.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition font-bold text-sm shadow-md hover:shadow-lg">
            <i class="fas fa-arrow-left"></i> Kembali ke Course Saya
        </a>
    </div>
    @endif
</div>
@endsection
