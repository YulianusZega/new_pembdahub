@extends('layouts.guru')
@section('title', 'Siswa Kelas ' . $classroom->class_name . ' - Portal Guru')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('guru.kelas') }}" class="text-sm text-emerald-600 hover:underline mb-1 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kelas Saya
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-graduate text-indigo-500"></i> {{ $classroom->class_name }}
            </h1>
        </div>
        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">
            {{ $classroom->students->count() }} Siswa
        </span>
    </div>

    @if($classroom->students->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold w-12">#</th>
                            <th class="px-5 py-3 text-left font-semibold">NISN</th>
                            <th class="px-5 py-3 text-left font-semibold">Nama Lengkap</th>
                            <th class="px-5 py-3 text-center font-semibold">L/P</th>
                            <th class="px-5 py-3 text-left font-semibold">Agama</th>
                            <th class="px-5 py-3 text-left font-semibold">No. HP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($classroom->students as $index => $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $student->nisn }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $student->full_name }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $student->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $student->gender }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $student->religion ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $student->phone ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-users-slash text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum ada siswa yang terdaftar di kelas ini.</p>
        </div>
    @endif
</div>
@endsection
