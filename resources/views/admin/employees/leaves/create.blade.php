@extends('layouts.admin')

@section('title', 'Ajukan Cuti / Izin')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.leaves.index') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-sky-600 shadow-lg">
                <i class="fas fa-file-circle-plus text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Ajukan Cuti / Izin</h1>
                <p class="text-gray-600 mt-1">Buat pengajuan cuti atau izin baru</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <ul class="text-red-800 text-sm list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.employees.leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pegawai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Pegawai *</label>
                    <select name="employee_id" required class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
                        <option value="">Pilih Pegawai</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} — {{ $emp->school->name ?? '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <!-- Jenis Cuti -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Jenis Cuti *</label>
                    <select name="leave_type" required class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
                        <option value="">Pilih Jenis</option>
                        @foreach($leaveTypes as $k => $v)
                        <option value="{{ $k }}" {{ old('leave_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required id="start_date"
                        class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
                </div>
                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Tanggal Selesai *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required id="end_date"
                        class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Days Preview -->
            <div id="days-preview" class="hidden p-4 bg-blue-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="text-blue-800 font-medium">Total: <strong id="days-count">0</strong> hari</span>
                    <span id="approval-note" class="text-blue-600 text-sm ml-2"></span>
                </div>
            </div>

            <!-- Alasan -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Alasan *</label>
                <textarea name="reason" rows="3" required placeholder="Jelaskan alasan pengajuan cuti/izin..."
                    class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all resize-none">{{ old('reason') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Lampiran -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Lampiran (Opsional)</label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 transition-all file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700">
                    <p class="text-xs text-gray-400 mt-1 px-1">PDF, JPG, PNG. Maks 5MB</p>
                </div>
                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Catatan (Opsional)</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-2.5 bg-gray-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-all">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.employees.leaves.index') }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">Batal</a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-cyan-600 to-sky-700 text-white rounded-xl font-medium hover:from-cyan-700 hover:to-sky-800 shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> Ajukan Cuti
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const preview = document.getElementById('days-preview');
    const daysCount = document.getElementById('days-count');
    const approvalNote = document.getElementById('approval-note');

    function calc() {
        if (startDate.value && endDate.value) {
            const s = new Date(startDate.value);
            const e = new Date(endDate.value);
            const diff = Math.ceil((e - s) / (1000 * 60 * 60 * 24)) + 1;
            if (diff > 0) {
                preview.classList.remove('hidden');
                daysCount.textContent = diff;
                approvalNote.textContent = diff > 3
                    ? '→ Perlu persetujuan Kepala Sekolah + Yayasan'
                    : '→ Persetujuan Kepala Sekolah saja';
            } else {
                preview.classList.add('hidden');
            }
        }
    }
    startDate.addEventListener('change', calc);
    endDate.addEventListener('change', calc);
    calc();
});
</script>
@endsection
