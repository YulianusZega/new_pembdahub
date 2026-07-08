@extends('layouts.admin')

@section('title', 'Input Absensi Massal')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.employees.attendance.index', ['date' => $date, 'school_id' => $school->id]) }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg">
                <i class="fas fa-list-check text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Input Absensi Massal</h1>
                <p class="text-gray-600 mt-1">{{ $school->name }} · {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.employees.attendance.bulk.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="school_id" value="{{ $school->id }}">

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-bold text-gray-500">Quick Set:</span>
                <button type="button" onclick="setAllStatus('hadir')" class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold hover:bg-green-200 transition-all">
                    <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                </button>
                <button type="button" onclick="setAllTime()" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl text-sm font-semibold hover:bg-blue-200 transition-all">
                    <i class="fas fa-clock mr-1"></i> Set Jam Default (07:15 - 15:00)
                </button>
                <button type="button" onclick="resetAll()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-4 text-left text-sm font-semibold w-8">No</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold">Pegawai</th>
                            <th class="px-4 py-4 text-center text-sm font-semibold w-40">Status</th>
                            <th class="px-4 py-4 text-center text-sm font-semibold w-32">Jam Masuk</th>
                            <th class="px-4 py-4 text-center text-sm font-semibold w-32">Jam Keluar</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($employees as $index => $emp)
                        @php $ex = $existing->get($emp->id); @endphp
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $emp->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $emp->employee_code }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <select name="attendance[{{ $emp->id }}][status]" class="att-status w-full px-3 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20">
                                    @foreach(\App\Models\EmployeeAttendance::STATUSES as $k => $v)
                                    <option value="{{ $k }}" {{ ($ex?->status ?? 'hadir') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="attendance[{{ $emp->id }}][time_in]" value="{{ $ex?->time_in }}"
                                    class="att-time-in w-full px-3 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 text-center">
                            </td>
                            <td class="px-4 py-3">
                                <input type="time" name="attendance[{{ $emp->id }}][time_out]" value="{{ $ex?->time_out }}"
                                    class="att-time-out w-full px-3 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 text-center">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="attendance[{{ $emp->id }}][notes]" value="{{ $ex?->notes }}" placeholder="Opsional"
                                    class="w-full px-3 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end mt-6 gap-3">
            <a href="{{ route('admin.employees.attendance.index', ['date' => $date, 'school_id' => $school->id]) }}"
                class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">Batal</a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-teal-600 to-emerald-700 text-white rounded-xl font-medium hover:from-teal-700 hover:to-emerald-800 shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Absensi
            </button>
        </div>
    </form>
</div>

<script>
function setAllStatus(status) {
    document.querySelectorAll('.att-status').forEach(s => s.value = status);
}
function setAllTime() {
    document.querySelectorAll('.att-time-in').forEach(i => { if(!i.value) i.value = '07:15'; });
    document.querySelectorAll('.att-time-out').forEach(o => { if(!o.value) o.value = '15:00'; });
}
function resetAll() {
    document.querySelectorAll('.att-status').forEach(s => s.value = 'hadir');
    document.querySelectorAll('.att-time-in').forEach(i => i.value = '');
    document.querySelectorAll('.att-time-out').forEach(o => o.value = '');
}
</script>
@endsection
