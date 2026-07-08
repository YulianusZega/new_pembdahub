@extends('layouts.admin')

@section('title', 'Detail Pegawai')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pegawai</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap pegawai non-kependidikan</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm">1</div>
                    <h2 class="text-xl font-bold text-gray-900">Data Pribadi</h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Kode Pegawai</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Nama Lengkap</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $employee->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Jenis Kelamin</label>
                        <p class="mt-1 text-gray-900">{{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Tempat, Tanggal Lahir</label>
                        <p class="mt-1 text-gray-900">{{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Agama</label>
                        <p class="mt-1 text-gray-900">{{ $employee->religion ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Sekolah</label>
                        <p class="mt-1 text-gray-900">{{ $employee->school->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm">2</div>
                    <h2 class="text-xl font-bold text-gray-900">Kontak & Alamat</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-phone mr-1"></i> Telepon</label>
                        <p class="mt-1 text-gray-900">{{ $employee->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-envelope mr-1"></i> Email Internal</label>
                        <p class="mt-1 text-gray-900">{{ $employee->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600"><i class="fas fa-home mr-1"></i> Alamat</label>
                        <p class="mt-1 text-gray-900">{{ $employee->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Kepegawaian -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm">3</div>
                    <h2 class="text-xl font-bold text-gray-900">Kepegawaian</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Jenis Pegawai</label>
                        <p class="mt-1">
                            @php
                                $types = [
                                    'staff_tu' => ['label' => 'Staff TU', 'class' => 'bg-purple-100 text-purple-700'],
                                    'staff_keuangan' => ['label' => 'Staff Keuangan', 'class' => 'bg-green-100 text-green-700'],
                                    'security' => ['label' => 'Security', 'class' => 'bg-red-100 text-red-700'],
                                    'cleaning_service' => ['label' => 'Cleaning Service', 'class' => 'bg-yellow-100 text-yellow-700'],
                                    'driver' => ['label' => 'Driver', 'class' => 'bg-blue-100 text-blue-700'],
                                    'other' => ['label' => 'Lainnya', 'class' => 'bg-gray-100 text-gray-700'],
                                ];
                                $type = $types[$employee->employee_type] ?? ['label' => ucfirst($employee->employee_type), 'class' => 'bg-gray-100 text-gray-700'];
                            @endphp
                            <span class="px-3 py-1 {{ $type['class'] }} text-xs font-bold rounded-lg">{{ $type['label'] }}</span>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-600">Status Kepegawaian</label>
                        <p class="mt-1">
                            @if($employee->employment_status)
                                @php
                                    $statusLabels = [
                                        'yayasan' => ['label' => 'Yayasan', 'class' => 'bg-indigo-100 text-indigo-700'],
                                        'pns' => ['label' => 'PNS', 'class' => 'bg-blue-100 text-blue-700'],
                                        'honorer' => ['label' => 'Honorer', 'class' => 'bg-amber-100 text-amber-700'],
                                    ];
                                    $st = $statusLabels[$employee->employment_status] ?? ['label' => ucfirst($employee->employment_status), 'class' => 'bg-gray-100 text-gray-700'];
                                @endphp
                                <span class="px-3 py-1 {{ $st['class'] }} text-xs font-bold rounded-lg">{{ $st['label'] }}</span>
                            @else
                                <span class="text-gray-400 italic text-sm">-</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-600">TMT (Tanggal Mulai Tugas)</label>
                        <p class="mt-1 text-gray-900">{{ $employee->tmt_date?->format('d M Y') ?? '-' }}</p>
                    </div>

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah() || auth()->user()->isKetuaYayasan() || auth()->user()->isBendahara())
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Gaji Pokok (Bulan)</label>
                        <p class="mt-1 text-gray-900 font-bold text-xl text-emerald-600">
                            Rp {{ number_format($employee->basic_salary ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-semibold text-gray-600">Status Aktif</label>
                        <div class="mt-1">
                            @if($employee->is_active)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full border border-gray-200">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Non-Aktif
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Photo Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Foto Pegawai</h3>
                </div>
                <div class="flex justify-center">
                    @if($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" 
                        class="w-44 h-44 rounded-3xl object-cover border-4 border-white shadow-lg ring-4 ring-gray-50">
                    @else
                    <div class="w-44 h-44 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center border-4 border-white shadow-lg ring-4 ring-gray-50">
                        <svg class="w-12 h-12 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-gray-500 text-xs font-medium">Belum ada foto</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Account -->
            @if($employee->user)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Akun Portal</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="mt-1 text-gray-900">{{ $employee->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Role</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                {{ ucfirst($employee->user->role) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.employees.edit', $employee) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Data
                    </a>
                    <a href="{{ route('admin.employees.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Pegawai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
