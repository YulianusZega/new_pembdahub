@extends('layouts.admin')

@section('title', 'Input Absensi Massal Guru')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.teachers.attendance.index', ['date' => $date, 'school_id' => $school->id]) }}"
           class="p-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
            <i class="fas fa-list-check text-xl text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Input Absensi Guru</h1>
            <p class="text-gray-500 text-sm mt-0.5">
                {{ $school->name }} &middot; {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Date Picker --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="school_id" value="{{ $school->id }}">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Ganti Tanggal</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors">
                <i class="fas fa-calendar-day mr-1"></i> Ganti Tanggal
            </button>
        </form>
    </div>

    @if($teachers->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-slash text-3xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 font-medium">Tidak ada guru aktif di sekolah ini</p>
        <p class="text-gray-400 text-sm mt-1">Pastikan data guru sudah diisi di menu Data Pegawai</p>
    </div>
    @else

    <form action="{{ route('admin.teachers.attendance.bulk.store') }}" method="POST" id="attendanceForm">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="school_id" value="{{ $school->id }}">

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-4 mb-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Quick Set:</span>
                <button type="button" onclick="setAllStatus('hadir')"
                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold hover:bg-green-200 transition-all">
                    <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                </button>
                <button type="button" onclick="setAllTime()"
                        class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-200 transition-all">
                    <i class="fas fa-clock mr-1"></i> Jam Default (07:15 – 15:00)
                </button>
                <button type="button" onclick="resetAll()"
                        class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
                <span class="ml-auto text-xs text-gray-400">{{ $teachers->count() }} guru</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase w-10">No</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Guru</th>
                            <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase w-44">Status</th>
                            <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase w-32">Jam Masuk</th>
                            <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase w-32">Jam Keluar</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($teachers as $index => $teacher)
                        @php $ex = $existing->get($teacher->id); @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($teacher->full_name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $teacher->full_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $teacher->employee_code ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <select name="attendance[{{ $teacher->id }}][status]"
                                        class="att-status w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                                    @foreach(\App\Models\EmployeeAttendance::STATUSES as $k => $v)
                                    <option value="{{ $k }}" {{ ($ex?->status ?? 'hadir') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="attendance[{{ $teacher->id }}][time_in]"
                                       value="{{ $ex?->time_in }}"
                                       class="att-time-in w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 text-center">
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="attendance[{{ $teacher->id }}][time_out]"
                                       value="{{ $ex?->time_out }}"
                                       class="att-time-out w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 text-center">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="attendance[{{ $teacher->id }}][notes]"
                                       value="{{ $ex?->notes }}" placeholder="Opsional"
                                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.teachers.attendance.index', ['date' => $date, 'school_id' => $school->id]) }}"
               class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all text-sm">
                Batal
            </a>
            <button type="submit" id="submitBtn"
                    class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 shadow-lg transition-all flex items-center gap-2 text-sm">
                <i class="fas fa-save"></i> Simpan Absensi Guru
            </button>
        </div>
    </form>
    @endif
</div>

<script>
function setAllStatus(status) {
    document.querySelectorAll('.att-status').forEach(s => s.value = status);
}
function setAllTime() {
    document.querySelectorAll('.att-time-in').forEach(i => { if (!i.value) i.value = '07:15'; });
    document.querySelectorAll('.att-time-out').forEach(o => { if (!o.value) o.value = '15:00'; });
}
function resetAll() {
    document.querySelectorAll('.att-status').forEach(s => s.value = 'hadir');
    document.querySelectorAll('.att-time-in').forEach(i => i.value = '');
    document.querySelectorAll('.att-time-out').forEach(o => o.value = '');
}

document.getElementById('attendanceForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});
</script>
@endsection
