@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-clock mr-2"></i>Manajemen Time Slot / Jam Pelajaran
        </h1>
        <a href="{{ route('admin.time-slots.create', ['school_id' => $selectedSchoolId, 'day' => $selectedDay]) }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-plus"></i>
            Tambah Time Slot
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <form method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filter Unit Sekolah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Sekolah</label>
                    @if(auth()->user()->isSuperAdmin())
                        <select name="school_id" id="schoolSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                    @else
                        <div class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 bg-indigo-50 text-gray-800 font-semibold">
                            <i class="fas fa-school mr-1"></i> {{ auth()->user()->school->name }}
                        </div>
                        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                    @endif
                </div>

                <!-- Day Tabs Hidden Input -->
                <input type="hidden" name="day" id="dayInput" value="{{ $selectedDay }}">
            </div>
        </form>
    </div>

    <!-- Day Tabs -->
    <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
        <div class="flex border-b">
            @foreach($days as $dayKey => $dayLabel)
                <button 
                    onclick="changeDay('{{ $dayKey }}')"
                    class="flex-1 px-4 py-3 text-center font-medium transition {{ $selectedDay == $dayKey ? 'bg-indigo-600 text-white border-b-2 border-indigo-600' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                    {{ $dayLabel }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Time Slots Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($timeSlots->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="width: 80px;">Urutan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Slot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="width: 100px;">Durasi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="width: 120px;">Mengajar?</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="width: 100px;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @foreach($timeSlots as $slot)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $slot->slot_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $slot->slot_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($slot->slot_type == 'lesson')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-book mr-1"></i> Pelajaran
                                    </span>
                                @elseif($slot->slot_type == 'break')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-coffee mr-1"></i> Istirahat
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-flag mr-1"></i> Upacara
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $slot->duration_minutes }} menit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($slot->is_teaching_slot)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Ya
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i> Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($slot->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.time-slots.edit', $slot) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.time-slots.destroy', $slot) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus time slot ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fas fa-clock text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">Belum ada time slot untuk hari {{ $days[$selectedDay] }}</p>
                <a href="{{ route('admin.time-slots.create', ['school_id' => $selectedSchoolId, 'day' => $selectedDay]) }}" 
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Time Slot
                </a>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong> Time slot adalah konfigurasi jam pelajaran untuk setiap hari. 
                    Anda bisa mengatur berbeda untuk Senin-Jumat. Time slot dengan "Mengajar = Ya" akan muncul di jadwal pelajaran.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function changeDay(day) {
    document.getElementById('dayInput').value = day;
    document.getElementById('filterForm').submit();
}

// Auto submit on school change
@if(auth()->user()->isSuperAdmin())
document.getElementById('schoolSelect').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
@endif
</script>
@endsection
