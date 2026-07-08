@extends('layouts.admin')

@section('title', 'Rekap Absensi TEFA')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900">Rekap Absensi TEFA</h1>
                <p class="text-sm text-gray-500">Bengkelin Tefa SMKS Pembda Nias | Waktu Kerja: 08.00 - 17.00 WIB (Senin - Sabtu)</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.tefa.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button onclick="openManualAttendanceModal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold text-sm shadow-lg shadow-orange-500/30 hover:from-orange-600 hover:to-amber-700 transition-all">
                <i class="fas fa-plus-circle"></i> Input Absensi Manual
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-2xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-green-500 text-lg"></i>
        <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <form action="{{ route('admin.tefa.attendances') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Bulan</label>
                <select name="month" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $m => $mName)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tahun</label>
                <select name="year" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Karyawan</label>
                <select name="tefa_employee_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Karyawan TEFA</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tanggal Khusus (Opsional)</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl text-sm shadow-md transition-all">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.tefa.attendances') }}" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-sm transition-all" title="Reset Filter">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">
                Riwayat Kehadiran Karyawan
            </h3>
            <span class="text-xs bg-orange-100 text-orange-800 font-bold px-3 py-1 rounded-full">
                Total: {{ $attendances->total() }} record
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6 w-12 text-center">No</th>
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Karyawan & Jabatan</th>
                        <th class="py-4 px-6 text-center">Jam Masuk</th>
                        <th class="py-4 px-6 text-center">Jam Pulang</th>
                        <th class="py-4 px-6 text-center">Durasi</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6">Keterangan</th>
                        <th class="py-4 px-6 text-center">Via</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($attendances as $index => $att)
                    @php
                        $duration = '-';
                        if ($att->time_in && $att->time_out && $att->time_out !== '00:00:00') {
                            $in = \Carbon\Carbon::parse($att->date->format('Y-m-d') . ' ' . $att->time_in);
                            $out = \Carbon\Carbon::parse($att->date->format('Y-m-d') . ' ' . $att->time_out);
                            if ($out->greaterThan($in)) {
                                $diff = $in->diff($out);
                                $duration = $diff->format('%h jam %i mnt');
                            }
                        }
                    @endphp
                    <tr class="hover:bg-orange-50/20 transition-all">
                        <td class="py-4 px-6 text-center font-bold text-gray-400">{{ $attendances->firstItem() + $index }}</td>
                        <td class="py-4 px-6 whitespace-nowrap font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d/m/Y') }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">{{ $att->employee->name ?? '-' }}</div>
                            <span class="text-xs text-gray-500">{{ $att->employee->position ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6 text-center font-mono font-bold text-green-600">
                            {{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-' }}
                        </td>
                        <td class="py-4 px-6 text-center font-mono font-bold text-blue-600">
                            {{ ($att->time_out && $att->time_out !== '00:00:00') ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-' }}
                        </td>
                        <td class="py-4 px-6 text-center font-semibold text-gray-600 text-xs">
                            {{ $duration }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($att->status === 'terlambat')
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">Terlambat</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">Hadir</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-xs text-gray-600">
                            {{ $att->notes ?: '-' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-mono text-[11px] font-bold uppercase">{{ $att->recorded_via }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditAttendanceModal({{ $att->toJson() }})"
                                    class="p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-orange-100 hover:text-orange-600 transition-all shadow-sm" title="Edit Absensi">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="{{ route('admin.tefa.attendances.destroy', $att->id) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-red-100 hover:text-red-600 transition-all shadow-sm" title="Hapus">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-gray-500">
                            <i class="fas fa-folder-open text-3xl mb-2 text-gray-300"></i>
                            <p class="font-bold">Belum ada data absensi untuk filter yang dipilih</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Input Manual -->
<div id="manualModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeManualAttendanceModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full p-6 text-left transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-orange-500"></i> Input Absensi Manual
                </h3>
                <button type="button" onclick="closeManualAttendanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.tefa.attendances.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Karyawan TEFA</label>
                    <select name="tefa_employee_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->position }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Absensi</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                            <option value="hadir">Hadir (Tepat Waktu)</option>
                            <option value="terlambat">Terlambat (> 08:00)</option>
                            <option value="izin">Izin / Lembur</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Masuk</label>
                        <input type="time" name="time_in" required value="08:00"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-orange-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Pulang (Opsional)</label>
                        <input type="time" name="time_out" value="17:00"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-orange-500/20">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Keterangan / Catatan</label>
                    <input type="text" name="notes" placeholder="Contoh: Hadir Tepat Waktu / Input Manual"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeManualAttendanceModal()"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-500/30 hover:from-orange-600 hover:to-amber-700 transition-all">
                        Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Absensi -->
<div id="editAttendanceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditAttendanceModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full p-6 text-left transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-orange-500"></i> Edit Data Absensi
                </h3>
                <button type="button" onclick="closeEditAttendanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editAttendanceForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Masuk</label>
                        <input type="time" name="time_in" id="edit_time_in" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-orange-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Pulang</label>
                        <input type="time" name="time_out" id="edit_time_out"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-orange-500/20">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                    <select name="status" id="edit_status" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-orange-500/20">
                        <option value="hadir">Hadir (Tepat Waktu)</option>
                        <option value="terlambat">Terlambat (> 08:00)</option>
                        <option value="izin">Izin / Lembur</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Keterangan / Catatan</label>
                    <input type="text" name="notes" id="edit_notes"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeEditAttendanceModal()"
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

@endsection

@push('scripts')
<script>
    function openManualAttendanceModal() {
        document.getElementById('manualModal').classList.remove('hidden');
    }
    function closeManualAttendanceModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }
    function openEditAttendanceModal(att) {
        document.getElementById('edit_time_in').value = att.time_in ? att.time_in.substring(0, 5) : '';
        document.getElementById('edit_time_out').value = (att.time_out && att.time_out !== '00:00:00') ? att.time_out.substring(0, 5) : '';
        document.getElementById('edit_status').value = att.status || 'hadir';
        document.getElementById('edit_notes').value = att.notes || '';
        document.getElementById('editAttendanceForm').action = '{{ url("admin/tefa/attendances") }}/' + att.id;
        document.getElementById('editAttendanceModal').classList.remove('hidden');
    }
    function closeEditAttendanceModal() {
        document.getElementById('editAttendanceModal').classList.add('hidden');
    }
</script>
@endpush
