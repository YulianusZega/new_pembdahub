@extends('layouts.guru')

@section('title', 'LMS - Portal Guru')

@push('styles')
<style>
    .course-card { animation: fadeUp 0.4s ease both; }
    .course-card:nth-child(1) { animation-delay: 0s; }
    .course-card:nth-child(2) { animation-delay: 0.06s; }
    .course-card:nth-child(3) { animation-delay: 0.12s; }
    .course-card:nth-child(4) { animation-delay: 0.18s; }
    .course-card:nth-child(5) { animation-delay: 0.24s; }
    .course-card:nth-child(6) { animation-delay: 0.30s; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hero-pattern {
        background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.08) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 40%);
    }
</style>
@endpush

@section('content')
<div class="space-y-8">
    {{-- ═══════════════════════════════════════════════ --}}
    {{-- HERO BANNER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="relative bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 rounded-2xl p-8 md:p-10 overflow-hidden shadow-lg border border-emerald-200">
        {{-- Decorative blobs --}}
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-200/30 rounded-full blur-2xl"></div>
        <div class="absolute -left-8 -bottom-8 w-48 h-48 bg-teal-200/20 rounded-full blur-xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-chalkboard-teacher text-xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-emerald-700 text-xs font-bold uppercase tracking-[0.2em]">Learning Management System</p>
                        <h2 class="text-2xl md:text-2xl font-bold text-gray-900 tracking-tight text-gray-900">Selamat Datang, {{ explode(' ', $teacher->user->name ?? 'Guru')[0] }}!</h2>
                    </div>
                </div>
                <p class="text-gray-600 text-sm max-w-md leading-relaxed">Kelola course, materi pembelajaran, tugas, dan quiz untuk kelas Anda.</p>
                
                @if($activeSemester)
                <div class="mt-4 inline-flex items-center gap-2 bg-emerald-100 border border-emerald-200 rounded-xl px-4 py-2">
                    <i class="fas fa-calendar-alt text-emerald-600 text-xs"></i>
                    <span class="text-sm font-semibold text-emerald-800">{{ $activeSemester->semester_name }}</span>
                </div>
                @endif
            </div>

            {{-- Quick Stats --}}
            <div class="flex items-center gap-3">
                @php
                    $totalMaterials = $courses->sum('materials_count');
                    $totalAssignments = $courses->sum('assignments_count');
                    $totalQuizzes = $courses->sum('quizzes_count');
                @endphp
                <div class="bg-white border border-emerald-200 rounded-xl px-5 py-3 text-center min-w-[80px] shadow-sm">
                    <div class="text-2xl font-bold leading-none text-emerald-700">{{ $courses->total() }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mt-1.5">Course</div>
                </div>
                <div class="bg-white border border-blue-200 rounded-xl px-5 py-3 text-center min-w-[80px] shadow-sm">
                    <div class="text-2xl font-bold leading-none text-blue-700">{{ $totalMaterials }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mt-1.5">Materi</div>
                </div>
                <div class="bg-white border border-purple-200 rounded-xl px-5 py-3 text-center min-w-[80px] shadow-sm">
                    <div class="text-2xl font-bold leading-none text-purple-700">{{ $totalAssignments + $totalQuizzes }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mt-1.5">Evaluasi</div>
                </div>
            </div>
        </div>

        {{-- Create button --}}
        <div class="relative z-10 mt-6 flex items-center gap-3">
            <a href="{{ route('guru.lms.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                <i class="fas fa-plus-circle"></i> Buat Course Baru
            </a>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════ --}}
    {{-- COURSE GRID --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($courses as $course)
        @php
            $colorConfig = \App\Models\LmsCourse::getColorClasses($course->color);
            $scientist = $course->getScientistConfig();
            $classNames = $course->lmsClasses->pluck('classroom.class_name')->filter()->implode(', ');
        @endphp
        <div class="course-card group">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col">
                {{-- Card Header --}}
                <div class="relative bg-gradient-to-br {{ $colorConfig['gradient'] }} p-5 overflow-hidden">
                    {{-- Subtle pattern --}}
                    <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 70% 30%, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        @if($scientist)
                        <svg class="w-28 h-28 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $scientist['icon'] !!}</svg>
                        @else
                        <i class="fas fa-book text-7xl text-white"></i>
                        @endif
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center border border-gray-100 flex-shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                                @if($scientist)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $scientist['icon'] !!}</svg>
                                @else
                                <i class="fas fa-book text-white"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-white text-base leading-snug line-clamp-2">{{ $course->name }}</h3>
                                <p class="text-white text-[10px] font-bold uppercase tracking-wider mt-0.5">{{ $course->subject->subject_name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="bg-white/20 px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest border border-gray-100">{{ $course->getShortCode() }}</span>
                            @if($course->is_published)
                            <span class="bg-emerald-400/30 px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest text-emerald-50 border border-emerald-400/20"><i class="fas fa-check-circle mr-0.5"></i> LIVE</span>
                            @else
                            <span class="bg-yellow-400/30 px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest text-yellow-50 border border-yellow-400/20">DRAFT</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 flex-1 flex flex-col">
                    {{-- Description --}}
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2 leading-relaxed flex-shrink-0">{{ $course->description ?: 'Kelola materi dan aktivitas belajar untuk ' . ($course->subject->subject_name ?? '') . '.' }}</p>
                    
                    {{-- Stats Row --}}
                    <div class="grid grid-cols-3 gap-2 mb-4 flex-shrink-0">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-2.5 rounded-xl text-center border border-emerald-100/80">
                            <div class="text-lg font-bold text-emerald-700 leading-none">{{ $course->materials_count }}</div>
                            <div class="text-[8px] font-bold text-emerald-500 mt-1 uppercase tracking-widest">Materi</div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 p-2.5 rounded-xl text-center border border-blue-100/80">
                            <div class="text-lg font-bold text-blue-700 leading-none">{{ $course->assignments_count }}</div>
                            <div class="text-[8px] font-bold text-blue-500 mt-1 uppercase tracking-widest">Tugas</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 p-2.5 rounded-xl text-center border border-purple-100/80">
                            <div class="text-lg font-bold text-purple-700 leading-none">{{ $course->quizzes_count }}</div>
                            <div class="text-[8px] font-bold text-purple-500 mt-1 uppercase tracking-widest">Quiz</div>
                        </div>
                    </div>

                    {{-- Classroom Info --}}
                    @if($classNames)
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-4 bg-gray-50 p-2.5 rounded-xl border border-gray-100 flex-shrink-0">
                        <i class="fas fa-users text-gray-300"></i>
                        <span class="truncate font-medium" title="{{ $classNames }}">{{ $classNames }}</span>
                    </div>
                    @endif
                    
                    {{-- CTA --}}
                    <div class="mt-auto">
                        <a href="{{ route('guru.lms.show', $course->id) }}"
                           class="flex items-center justify-center gap-2 w-full {{ $colorConfig['bg'] }} hover:brightness-110 text-white px-4 py-3 rounded-xl transition-all shadow-sm hover:shadow-lg text-sm font-bold group-hover:-translate-y-0.5 active:translate-y-0">
                            <i class="fas fa-arrow-right-to-bracket"></i> Kelola Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
    
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i class="fas fa-book-open text-4xl text-emerald-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Course</h3>
        <p class="text-gray-500 mb-8 max-w-sm mx-auto leading-relaxed">Mulai buat course pertama Anda untuk mengelola materi pembelajaran secara digital.</p>
        <a href="{{ route('guru.lms.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-8 py-3.5 rounded-xl hover:bg-emerald-700 transition-all shadow-lg hover:shadow-xl font-bold text-sm">
            <i class="fas fa-plus-circle"></i> Buat Course Pertama
        </a>
    </div>
    @endif
</div>
@endsection
