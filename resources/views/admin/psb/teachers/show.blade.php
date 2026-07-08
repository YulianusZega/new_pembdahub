@extends('layouts.admin')

@section('title', 'Detail Guru')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Guru</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap guru</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">1</div>
                    <h2 class="text-xl font-bold text-gray-900">Data Pribadi</h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Kode Guru</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $teacher->teacher_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Nama Lengkap</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $teacher->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Jenis Kelamin</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Tempat, Tanggal Lahir</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->birth_place ?? '-' }}, {{ $teacher->birth_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Agama</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->religion ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Sekolah</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->school->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-graduation-cap mr-1"></i> Pendidikan Terakhir</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->education_level ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-graduation-cap mr-1"></i> Jurusan</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->major ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">2</div>
                    <h2 class="text-xl font-bold text-gray-900">Kontak & Alamat</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-phone mr-1"></i> Telepon</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-home mr-1"></i> Alamat</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Kepegawaian -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">3</div>
                    <h2 class="text-xl font-bold text-gray-900">Kepegawaian</h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Jabatan/Posisi</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->position ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Status</label>
                        <div class="mt-1">
                            @if($teacher->is_active)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                Non-Aktif
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kompetensi Mata Pelajaran -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 text-white font-bold text-sm">4</div>
                        <h2 class="text-xl font-bold text-gray-900">Kompetensi Mata Pelajaran</h2>
                    </div>
                    <a href="{{ route('admin.teachers.competencies', $teacher) }}" 
                        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all shadow-md text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Kelola Kompetensi
                    </a>
                </div>

                @if($teacher->competentSubjects && $teacher->competentSubjects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($teacher->competentSubjects as $subject)
                    <div class="relative p-4 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl hover:shadow-lg transition-all">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($subject->subject_name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $subject->subject_name }}</h3>
                                <p class="text-xs text-gray-600 mt-1">{{ $subject->subject_code }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-sm text-purple-900">
                        <span class="font-semibold">Total Kompetensi:</span> {{ $teacher->competentSubjects->count() }} mata pelajaran
                    </p>
                </div>
                @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada kompetensi mata pelajaran yang ditetapkan</p>
                    <p class="text-gray-400 text-xs mt-1">Klik tombol "Kelola Kompetensi" untuk menambahkan</p>
                </div>
                @endif
            </div>

            <!-- Mata Pelajaran yang Diajar (dari jadwal) -->
            @if($teacher->subjects->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">5</div>
                    <h2 class="text-xl font-bold text-gray-900">Mata Pelajaran yang Sedang Diajar</h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($teacher->subjects as $subject)
                    <span class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold rounded-full">
                        {{ $subject->subject_name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Jadwal Mengajar -->
            @if($teacher->schedules->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">6</div>
                    <h2 class="text-xl font-bold text-gray-900">Jadwal Mengajar</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Hari</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Waktu</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kelas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($teacher->schedules as $schedule)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $schedule->day }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $schedule->subject->subject_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $schedule->classroom->class_name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Photo Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Foto Guru</h3>
                <div class="flex justify-center">
                    @if($teacher->photo)
                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}" 
                        class="w-44 h-44 rounded-3xl object-cover border-4 border-white shadow-lg ring-4 ring-gray-50">
                    @else
                    <div class="w-44 h-44 rounded-3xl bg-gradient-to-br from-emerald-400 to-teal-500 flex flex-col items-center justify-center border-4 border-white shadow-lg ring-4 ring-emerald-50 text-white font-bold">
                        <span class="text-6xl">{{ substr($teacher->full_name, 0, 1) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Account -->
            @if($teacher->user)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Akun Portal</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="mt-1 text-gray-900">{{ $teacher->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Role</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">
                                {{ ucfirst($teacher->user->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Status Akun</label>
                        <p class="mt-1">
                            @if($teacher->user->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Aktif</span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Non-Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4">
                <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Guru ini belum memiliki akun portal</p>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Data
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Guru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
