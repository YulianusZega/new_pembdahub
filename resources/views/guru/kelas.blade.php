@extends('layouts.guru')
@section('title', 'Kelas Saya - Portal Guru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                </span>
                Kelas Saya
            </h1>
            <p class="text-gray-500 text-sm mt-1 ml-11">Daftar kelas yang Anda ampu</p>
        </div>
        @if($activeYear)
            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-xl text-sm font-semibold">{{ $activeYear->year }}</span>
        @endif
    </div>

    @if($classrooms->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($classrooms as $cr)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="bg-gradient-to-r {{ $homeroomClassroom && $homeroomClassroom->id === $cr->id ? 'from-emerald-500 to-green-500' : 'from-indigo-500 to-blue-500' }} p-4 text-white">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-lg">{{ $cr->class_name }}</h3>
                            @if($homeroomClassroom && $homeroomClassroom->id === $cr->id)
                                <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full"><i class="fas fa-star mr-1"></i>Wali Kelas</span>
                            @endif
                        </div>
                        <p class="text-white/80 text-sm mt-1">{{ $cr->school->name ?? '' }}</p>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500">Siswa</p>
                                <p class="font-bold text-lg text-gray-800">{{ $cr->students_count }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500">Tingkat</p>
                                <p class="font-bold text-lg text-gray-800">{{ $cr->grade_level ?? '-' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('guru.siswa-kelas', $cr->id) }}"
                           class="block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2.5 rounded-xl transition">
                            <i class="fas fa-eye mr-1"></i> Lihat Siswa
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-chalkboard text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum ada kelas yang ditugaskan untuk Anda.</p>
        </div>
    @endif
</div>
@endsection
