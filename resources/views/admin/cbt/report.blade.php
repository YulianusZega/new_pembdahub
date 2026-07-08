@extends('layouts.admin')
@section('title', 'Laporan CBT')
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan CBT</h1>
                    <p class="text-gray-800 mt-1">Statistik dan analisis ujian online</p>
                </div>
            </div>
            <a href="{{ route('admin.cbt.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="text-base text-gray-700">Total Ujian</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $examStats->total_exams ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="text-base text-gray-700">Ujian Selesai</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $examStats->completed ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="text-base text-gray-700">Rata-rata Nilai</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($resultStats->avg_score ?? 0, 1) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="text-base text-gray-700">Tingkat Kelulusan</div>
            <div class="text-2xl font-bold text-green-600 mt-1">
                {{ $resultStats->total_results > 0 ? number_format(($resultStats->passed / $resultStats->total_results) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Per Subject -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Performa per Mata Pelajaran</h2>
            @forelse($subjectPerformance as $sp)
            <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                <div>
                    <div class="text-base font-medium text-gray-900">{{ $sp->subject_name }}</div>
                    <div class="text-base text-gray-700">{{ $sp->total_taken }} peserta</div>
                </div>
                <div class="text-right">
                    <div class="text-base font-bold {{ $sp->avg_score >= 70 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($sp->avg_score, 1) }}</div>
                    <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                        <div class="h-2 rounded-full {{ $sp->avg_score >= 70 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, $sp->avg_score) }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-700 text-center py-4">Belum ada data.</p>
            @endforelse
        </div>

        <!-- Per Classroom -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Performa per Kelas</h2>
            @forelse($classroomPerformance as $cp)
            <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                <div>
                    <div class="text-base font-medium text-gray-900">{{ $cp->classroom_name }}</div>
                    <div class="text-base text-gray-700">{{ $cp->total_taken }} peserta</div>
                </div>
                <div class="text-right">
                    <div class="text-base font-bold {{ $cp->avg_score >= 70 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($cp->avg_score, 1) }}</div>
                    <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                        <div class="h-2 rounded-full {{ $cp->avg_score >= 70 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, $cp->avg_score) }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-700 text-center py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
