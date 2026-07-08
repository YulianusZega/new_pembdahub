@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @php
                    $avatar = $classroom->getAvatarConfig();
                @endphp

                @if($avatar['icon'])
                {{-- Predefined Avatar with unique gradient + custom SVG icon --}}
                <div class="w-16 h-16 bg-gradient-to-br {{ $avatar['gradient'] }} rounded-2xl flex items-center justify-center shadow-lg ring-2 {{ $avatar['ring'] }}">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $avatar['icon'] !!}
                    </svg>
                </div>
                @else
                {{-- Beautiful letter icon with dynamic gradient --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br {{ $avatar['gradient'] }} shadow-lg text-white text-2xl font-bold ring-2 {{ $avatar['ring'] ?? 'ring-white/20' }}">
                    {{ $avatar['initials'] }}
                </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $classroom->class_name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                            Kelas {{ $classroom->grade_level }}
                        </span>
                        @if($classroom->is_active)
                        <span class="flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Aktif
                        </span>
                        @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Kelas -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Kelas</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                            <i class="fas fa-school mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Sekolah</p>
                            <p class="text-base font-semibold text-gray-900">{{ optional($classroom->school)->name ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fas fa-chart-bar mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Tingkat</p>
                            <p class="text-base font-semibold text-gray-900">Kelas {{ $classroom->grade_level }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                            <i class="fas fa-users mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Kapasitas</p>
                            <p class="text-base font-semibold text-gray-900">{{ $classroom->capacity ?? '-' }} siswa</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fas fa-edit mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Jumlah Siswa</p>
                            @php
                                $count = $students->count();
                                $capacity = $classroom->capacity ?? 0;
                                $percentage = $capacity > 0 ? ($count / $capacity) * 100 : 0;
                                $colorClass = $percentage >= 90 ? 'bg-red-100 text-red-800' : 
                                              ($percentage >= 75 ? 'bg-yellow-100 text-yellow-800' : 
                                              'bg-green-100 text-green-800');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                                {{ $count }} / {{ $capacity }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Siswa -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Daftar Siswa ({{ $students->count() }})</h2>
                </div>
                
                @if($students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">NISN</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sekolah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($students as $student)
                            <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden">
                                            <img src="{{ $student->photo_url }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $student->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $student->nisn }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ optional($student->school)->name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg mb-2">Belum ada siswa di kelas ini</p>
                    <p class="text-gray-400 text-sm">Gunakan fitur "Assign Students" untuk menambah siswa</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.classrooms.edit', $classroom) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Kelas
                    </a>
                    
                    <a href="{{ route('admin.classrooms.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                    
                    <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection