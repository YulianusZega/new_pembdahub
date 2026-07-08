@extends('layouts.admin')

@section('title', 'Absensi TEFA - Bengkelin')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-amber-500 via-orange-600 to-red-600 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-20 -bottom-10 w-48 h-48 bg-black/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 shadow-inner">
                    <i class="fas fa-tools text-3xl md:text-4xl text-white"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold mb-2 backdrop-blur-sm border border-white/20">
                        <i class="fas fa-industry"></i> UNIT PRODUKSI TEFA
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Bengkelin Tefa SMKS Pembda Nias</h1>
                    <p class="text-white/80 text-sm md:text-base mt-1">Monitoring kehadiran & jam kerja khusus karyawan unit produksi (Terpisah dari SDM Sekolah)</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.tefa.attendances') }}"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold text-sm backdrop-blur-md border border-white/30 transition-all shadow-lg hover:scale-105">
                    <i class="fas fa-history"></i> Rekap & Riwayat Absensi
                </a>
                <button onclick="openAddEmployeeModal()"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-orange-600 hover:bg-orange-50 font-black text-sm shadow-xl hover:scale-105 transition-all">
                    <i class="fas fa-plus-circle"></i> Tambah Karyawan
                </button>
            </div>
        </div>

        <!-- Aturan Kerja Banner Footer -->
        <div class="mt-6 pt-6 border-t border-white/20 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs md:text-sm">
            <div class="flex items-center gap-3 bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0 font-bold"><i class="fas fa-clock"></i></div>
                <div>
                    <span class="text-white/70 block text-[11px] uppercase tracking-wider font-bold">Waktu Kerja Wajib</span>
                    <strong class="text-white font-mono text-sm">08.00 - 17.00 WIB</strong>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0 font-bold"><i class="fas fa-calendar-week"></i></div>
                <div>
                    <span class="text-white/70 block text-[11px] uppercase tracking-wider font-bold">Hari Kerja</span>
                    <strong class="text-white">Senin s.d. Sabtu</strong>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0 font-bold"><i class="fas fa-wifi"></i></div>
                <div>
                    <span class="text-white/70 block text-[11px] uppercase tracking-wider font-bold">Infrastruktur Scan</span>
                    <strong class="text-white">Station Kiosk Eksisting (Tanpa Alat Baru)</strong>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-2xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-green-500 text-lg"></i>
        <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
        <p class="text-red-800 font-medium text-sm">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Karyawan TEFA</p>
                <p class="text-3xl font-black text-gray-800 mt-1">{{ $stats['total'] }} <span class="text-xs font-normal text-gray-500">orang</span></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Hadir Hari Ini</p>
                <p class="text-3xl font-black text-green-600 mt-1">{{ $stats['hadir'] }} <span class="text-xs font-normal text-gray-500">orang</span></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-user-check text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Terlambat (> 08.00)</p>
                <p class="text-3xl font-black text-amber-600 mt-1">{{ $stats['terlambat'] }} <span class="text-xs font-normal text-gray-500">orang</span></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum Hadir</p>
                <p class="text-3xl font-black text-gray-500 mt-1">{{ $stats['belum'] }} <span class="text-xs font-normal text-gray-500">orang</span></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500">
                <i class="fas fa-user-clock text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Table Karyawan -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-id-card-clip text-orange-500"></i> Daftar Karyawan & Status Absensi Hari Ini
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Tanggal: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span></p>
            </div>
            <div class="flex items-center gap-2 text-xs bg-orange-50 text-orange-700 px-3 py-1.5 rounded-xl font-medium border border-orange-200/60">
                <i class="fas fa-info-circle"></i> Gunakan kartu RFID eksisting di station absensi untuk scan masuk dan pulang.
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6 w-12 text-center">No</th>
                        <th class="py-4 px-6">Nama & Jabatan</th>
                        <th class="py-4 px-6">No HP</th>
                        <th class="py-4 px-6">Kartu RFID</th>
                        <th class="py-4 px-6 text-center">Jam Masuk</th>
                        <th class="py-4 px-6 text-center">Jam Pulang</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($employees as $index => $emp)
                    @php
                        $att = $emp->attendances->first();
                    @endphp
                    <tr class="hover:bg-orange-50/30 transition-all">
                        <td class="py-4 px-6 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">{{ $emp->name }}</div>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 text-[11px] font-semibold">
                                {{ $emp->position }}
                            </span>
                        </td>
                        <td class="py-4 px-6 font-mono text-gray-600 text-xs">
                            {{ $emp->phone ?: '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @if($emp->rfid_uid)
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-purple-50 text-purple-700 font-mono text-xs font-bold border border-purple-200 flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-wifi text-purple-500"></i> {{ $emp->rfid_uid }}
                                </span>
                                <button type="button" onclick="openRfidModal('{{ addslashes($emp->name) }}', '{{ $emp->rfid_uid }}', '{{ route('admin.tefa.employees.register-rfid', $emp->id) }}', 'Karyawan TEFA')"
                                    class="text-gray-400 hover:text-purple-600 p-1.5 rounded-lg hover:bg-purple-50 transition-all" title="Ganti Kartu RFID">
                                    <i class="fas fa-sync-alt text-xs"></i>
                                </button>
                            </div>
                            @else
                            <button type="button" onclick="openRfidModal('{{ addslashes($emp->name) }}', '', '{{ route('admin.tefa.employees.register-rfid', $emp->id) }}', 'Karyawan TEFA')"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm shadow-purple-500/20 transition-all animate-pulse">
                                <i class="fas fa-wifi"></i> Scan / Daftarkan RFID
                            </button>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center font-mono font-bold">
                            @if($att && $att->time_in)
                                <span class="text-green-600 bg-green-50 px-2.5 py-1 rounded-lg border border-green-200/60">{{ \Carbon\Carbon::parse($att->time_in)->format('H:i') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center font-mono font-bold">
                            @if($att && $att->time_out && $att->time_out !== '00:00:00')
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200/60">{{ \Carbon\Carbon::parse($att->time_out)->format('H:i') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($att)
                                @if($att->status === 'terlambat')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                        <i class="fas fa-clock text-[10px]"></i> Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">
                                        <i class="fas fa-check-circle text-[10px]"></i> Hadir
                                    </span>
                                @endif
                                @if($att->notes)
                                    <div class="text-[11px] text-gray-500 mt-1">{{ $att->notes }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">
                                    Belum Hadir
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditEmployeeModal({{ $emp->toJson() }})"
                                    class="p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-orange-100 hover:text-orange-600 transition-all shadow-sm" title="Edit Profil">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="{{ route('admin.tefa.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-red-100 hover:text-red-600 transition-all shadow-sm" title="Hapus Karyawan">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-users-slash text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-bold text-gray-700">Belum ada data Karyawan TEFA</p>
                                <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Karyawan" di atas untuk menambahkan 3 karyawan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Kehadiran Terbaru Hari Ini -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-broadcast-tower text-green-500 animate-pulse"></i> Log Scan Kehadiran Realtime Hari Ini
            </h3>
            <span class="text-xs bg-green-100 text-green-800 font-bold px-3 py-1 rounded-full">
                {{ $recentAttendances->count() }} Aktivitas
            </span>
        </div>
        <div class="p-6">
            @if($recentAttendances->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-history text-3xl mb-2 text-gray-300"></i>
                <p class="text-sm font-medium">Belum ada aktivitas scan kehadiran hari ini di station kiosk.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentAttendances as $log)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50/80 border border-gray-100/80 hover:bg-orange-50/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">{{ $log->employee->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $log->employee->position }} | Via: <span class="uppercase font-mono font-bold">{{ $log->recorded_via }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <div class="text-xs font-semibold text-gray-400 uppercase">Jam Masuk</div>
                            <div class="font-mono font-bold text-green-600 text-sm">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i:s') : '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold text-gray-400 uppercase">Jam Pulang</div>
                            <div class="font-mono font-bold text-blue-600 text-sm">{{ ($log->time_out && $log->time_out !== '00:00:00') ? \Carbon\Carbon::parse($log->time_out)->format('H:i:s') : '-' }}</div>
                        </div>
                        <div>
                            @if($log->status === 'terlambat')
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs">Terlambat</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold text-xs">Hadir</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddEmployeeModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full p-6 text-left transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-orange-500"></i> Tambah Karyawan TEFA
                </h3>
                <button type="button" onclick="closeAddEmployeeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.tefa.employees.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap Karyawan</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jabatan / Posisi di Bengkelin</label>
                    <input type="text" name="position" required placeholder="Contoh: Mekanik Kepala / Teknisi"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">No HP / WhatsApp (Opsional)</label>
                    <input type="text" name="phone" placeholder="Contoh: 081234567890"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kartu RFID UID (Opsional)</label>
                    <input type="text" name="rfid_uid" placeholder="Bisa didaftarkan nanti via scan"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-mono uppercase">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddEmployeeModal()"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-500/30 hover:from-orange-600 hover:to-amber-700 transition-all">
                        Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditEmployeeModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full p-6 text-left transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-orange-500"></i> Edit Profil Karyawan TEFA
                </h3>
                <button type="button" onclick="closeEditEmployeeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editEmployeeForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap Karyawan</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jabatan / Posisi di Bengkelin</label>
                    <input type="text" name="position" id="edit_position" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">No HP / WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-mono">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeEditEmployeeModal()"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-500/30 hover:from-orange-600 hover:to-amber-700 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.rfid-modal')

@endsection

@push('scripts')
<script>
    function openAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.remove('hidden');
    }
    function closeAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.add('hidden');
    }
    function openEditEmployeeModal(emp) {
        document.getElementById('edit_name').value = emp.name;
        document.getElementById('edit_position').value = emp.position;
        document.getElementById('edit_phone').value = emp.phone || '';
        document.getElementById('editEmployeeForm').action = '{{ url("admin/tefa/employees") }}/' + emp.id;
        document.getElementById('editEmployeeModal').classList.remove('hidden');
    }
    function closeEditEmployeeModal() {
        document.getElementById('editEmployeeModal').classList.add('hidden');
    }
</script>
@endpush
