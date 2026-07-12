@extends('layouts.admin')

@section('title', 'Monitoring LMS - PembdaHUB')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Monitoring Keaktifan LMS</h2>
        <p class="text-sm text-gray-500">Pantau penggunaan platform pembelajaran oleh Guru dan Siswa</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">
            Live Monitoring
        </span>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Courses -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg shadow-blue-200">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Total Materi/Kursus</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalCourses) }}</h3>
        </div>
    </div>

    <!-- Total Enrollments -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg shadow-emerald-200">
                <i class="fas fa-user-graduate text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Siswa Terdaftar</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalEnrollments) }}</h3>
        </div>
    </div>

    <!-- Total Submissions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg shadow-orange-200">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Tugas Dikumpul</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalSubmissions) }}</h3>
        </div>
    </div>

    <!-- Total Discussions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg shadow-purple-200">
                <i class="fas fa-comments text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Interaksi Diskusi</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalDiscussions) }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Active Courses -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-fire text-orange-500"></i> Materi Paling Aktif
            </h3>
            <span class="text-xs text-gray-400">Berdasarkan Tugas Terkumpul</span>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @foreach($activeCourses as $course)
                <div class="flex items-center gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-graduation-cap text-indigo-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-800 truncate">{{ $course->course_name }}</h4>
                        <p class="text-xs text-gray-500">{{ optional($course->teacher->user)->name ?? 'Guru Pengampu' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-indigo-600">{{ $course->submissions_count }}</div>
                        <div class="text-[10px] text-gray-400 uppercase font-medium">Submissions</div>
                    </div>
                </div>
                @endforeach
                @if($activeCourses->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3 opacity-20"></i>
                    <p>Belum ada aktivitas pembelajaran utama</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Latest Activities -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i> Aktivitas Terkini
            </h3>
            <span class="animate-pulse flex h-2 w-2 rounded-full bg-red-400"></span>
        </div>
        <div class="p-0">
            <div class="divide-y divide-gray-50">
                @foreach($latestSubmissions as $submission)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                            <i class="fas fa-file-upload text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">
                                <span class="font-bold">{{ optional($submission->student->user)->name ?? 'Siswa' }}</span> 
                                mengumpulkan tugas pada materi 
                                <span class="font-semibold text-indigo-600">{{ optional($submission->assignment->course)->course_name ?? 'Materi' }}</span>
                            </p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-medium">
                                <i class="far fa-clock mr-1"></i> {{ $submission->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($latestSubmissions->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-stream text-4xl mb-3 opacity-20"></i>
                    <p>Belum ada pengumpulan tugas terbaru hari ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection
