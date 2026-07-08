@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-full overflow-hidden shadow-md ring-2 ring-indigo-100 flex-shrink-0">
                    <img src="{{ $student->photo_url }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $student->full_name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                            {{ $student->nisn }}
                        </span>
                        @php
                        $statusColors = [
                            'aktif' => 'bg-green-100 text-green-800',
                            'lulus' => 'bg-blue-100 text-blue-800',
                            'keluar' => 'bg-red-100 text-red-800',
                            'pindah' => 'bg-yellow-100 text-yellow-800',
                        ];
                        $statusColor = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 {{ $statusColor }} rounded-full text-sm font-semibold">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.payments', $student) }}" 
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Riwayat Pembayaran
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Data Pribadi</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fas fa-school mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Sekolah</p><p class="text-base font-semibold text-gray-900">{{ $student->school?->name ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600"><i class="fas fa-list-ol mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">NISN / NIS</p><p class="text-base font-semibold text-gray-900">{{ $student->nisn }} / {{ $student->nis ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-venus-mars mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Jenis Kelamin</p><p class="text-base font-semibold text-gray-900">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600"><i class="fas fa-calendar-alt mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Tahun Masuk</p><p class="text-base font-semibold text-gray-900">{{ $student->entry_year }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600"><i class="fas fa-map-marker-alt mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Tempat, Tanggal Lahir</p><p class="text-base font-semibold text-gray-900">{{ $student->birth_place ?? '-' }}, {{ optional($student->birth_date)->format('d M Y') ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-pink-50 text-pink-600"><i class="fas fa-pray mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Agama</p><p class="text-base font-semibold text-gray-900">{{ $student->religion ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-teal-50 text-teal-600"><i class="fas fa-phone mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Telepon</p><p class="text-base font-semibold text-gray-900">{{ $student->phone ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-50 text-cyan-600"><i class="fas fa-school mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Asal Sekolah</p><p class="text-base font-semibold text-gray-900">{{ $student->previous_school ?? '-' }}</p></div>
                    </div>
                    <div class="md:col-span-2 flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600"><i class="fas fa-map-marker-alt mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Alamat</p><p class="text-base font-semibold text-gray-900">{{ $student->address ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
            
            <!-- Data Wali -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Data Wali</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600"><i class="fas fa-users mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Nama Wali</p><p class="text-base font-semibold text-gray-900">{{ $student->guardian_name ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-phone mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Telepon Wali</p><p class="text-base font-semibold text-gray-900">{{ $student->guardian_phone ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600"><i class="fas fa-briefcase mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Pekerjaan</p><p class="text-base font-semibold text-gray-900">{{ $student->guardian_occupation ?? '-' }}</p></div>
                    </div>
                    <div class="md:col-span-2 flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600"><i class="fas fa-map-marker-alt mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Alamat Wali</p><p class="text-base font-semibold text-gray-900">{{ $student->guardian_address ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
            
            <!-- Info Tambahan -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Tambahan</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600"><i class="fas fa-palette mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Hobi</p><p class="text-base font-semibold text-gray-900">{{ $student->hobby ?? '-' }}</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600"><i class="fas fa-hospital mr-1"></i></div>
                        <div class="flex-1"><p class="text-sm text-gray-500 mb-1">Riwayat Kesehatan</p><p class="text-base font-semibold text-gray-900">{{ $student->health_history ?? '-' }}</p></div>
                    </div>
                </div>
            </div>

            <!-- Catatan Perkembangan Siswa -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Catatan Perkembangan Siswa</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Kasus</th>
                                <th class="px-4 py-3">Severity</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->counselingRecords()->latest()->get() as $record)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $record->incident_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $record->title }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($record->category) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-lg font-semibold @if($record->severity === 'kritis') bg-red-100 text-red-800 @elseif($record->severity === 'berat') bg-orange-100 text-orange-800 @elseif($record->severity === 'sedang') bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($record->severity) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-lg @if($record->status === 'selesai') bg-green-100 text-green-800 @elseif($record->status === 'tindak_lanjut') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.counseling.show', $record) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada catatan konseling.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="space-y-6">
            <!-- Statistik Kehadiran Kumulatif -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 shadow-xl text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-chart-line text-indigo-300"></i>
                        <h3 class="text-sm font-bold uppercase tracking-widest">Kehadiran Aktif</h3>
                    </div>
                    
                    <div class="flex items-end justify-between mb-4">
                        <div class="text-4xl font-bold">{{ $attendanceStats['presence_rate'] }}%</div>
                        <div class="text-[10px] text-indigo-200 font-bold uppercase text-right leading-tight">
                            {{ $attendanceStats['hadir'] }} Hadir<br>
                            {{ $attendanceStats['z_days'] }} Hari Aktif
                        </div>
                    </div>

                    <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden mb-6">
                        <div class="h-full bg-white shadow-sm transition-all duration-1000" style="width: {{ $attendanceStats['presence_rate'] }}%"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 p-2.5 rounded-2xl border border-white/5">
                            <p class="text-[9px] font-bold text-indigo-200 uppercase mb-0.5">Izin</p>
                            <p class="text-lg font-bold">{{ $attendanceStats['izin'] }}</p>
                        </div>
                        <div class="bg-white/10 p-2.5 rounded-2xl border border-white/5">
                            <p class="text-[9px] font-bold text-indigo-200 uppercase mb-0.5">Sakit</p>
                            <p class="text-lg font-bold">{{ $attendanceStats['sakit'] }}</p>
                        </div>
                    </div>
                    <div class="mt-3 bg-red-500/20 p-2.5 rounded-2xl border border-red-500/20">
                        <div class="flex items-center justify-between">
                            <p class="text-[9px] font-bold text-red-200 uppercase">Alpha (Tanpa Ket)</p>
                            <p class="text-lg font-bold text-red-100">{{ $attendanceStats['alpha'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Foto Siswa -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Foto Siswa</h3>
                </div>
                <div class="flex justify-center">
                    <img src="{{ $student->photo_url }}" 
                        class="w-44 h-44 rounded-3xl object-cover border-4 border-white shadow-lg ring-4 ring-gray-50" 
                        alt="Foto {{ $student->full_name }}">
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.students.edit', $student) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Siswa
                    </a>
                    <a href="{{ route('admin.students.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection