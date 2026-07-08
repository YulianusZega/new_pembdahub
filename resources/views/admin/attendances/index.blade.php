@extends('layouts.admin')

@section('title', 'Daftar Absensi - Admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Absensi</h1>
                <p class="text-gray-600">Kelola kehadiran siswa</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.attendances.monitoring') }}" class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white px-4 py-2 rounded-xl font-semibold shadow transition duration-200 flex items-center transform hover:scale-105">
                <span class="w-2.5 h-2.5 bg-white rounded-full animate-pulse mr-2"></span> Live Monitoring
            </a>
            <a href="{{ route('admin.attendances.mass-update') }}" class="bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white px-4 py-2 rounded-xl font-semibold shadow transition duration-200">
                <i class="fas fa-layer-group mr-1"></i> Mass Update
            </a>
            <a href="{{ route('admin.attendances.create') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2 rounded-xl font-semibold shadow transition duration-200 transform hover:scale-105">
                <i class="fas fa-plus mr-1"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-red-800 font-bold">Gagal memproses aksi:</h3>
                <ul class="list-disc list-inside text-red-700 text-sm mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <span class="text-green-700 font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Modern Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun Pelajaran</label>
                <select name="academic_year_id" id="academic_year_filter" class="w-full px-3 py-2 border-2 border-gray-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm bg-white">
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ $selectedAcademicYearId == $ay->id ? 'selected' : '' }}>
                        {{ $ay->year }} {{ $ay->is_active ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Tanggal</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" name="date" value="{{ $filters['date'] }}" class="w-full pl-10 pr-3 py-2 border-2 border-gray-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                </div>
            </div>

            @if($isSuperAdmin)
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Sekolah</label>
                <select name="school_id" id="school_filter" class="w-full px-3 py-2 border-2 border-gray-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    <option value="">Semua Sekolah</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $filters['school_id'] == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelas</label>
                <select name="classroom_id" id="classroom_filter" class="w-full px-3 py-2 border-2 border-gray-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $class)
                    <option value="{{ $class->id }}" data-school-id="{{ $class->school_id }}" data-academic-year-id="{{ $class->academic_year_id }}" {{ $filters['classroom_id'] == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border-2 border-gray-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' }}>✅ Hadir</option>
                    <option value="terlambat" {{ ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' }}>⏰ Terlambat</option>
                    <option value="izin" {{ ($filters['status'] ?? '') == 'izin' ? 'selected' : '' }}>📝 Izin</option>
                    <option value="sakit" {{ ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' }}>🤒 Sakit</option>
                    <option value="alpha" {{ ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' }}>❌ Alpa</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-xl font-semibold transition shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.attendances.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl font-semibold transition border border-gray-200">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-sm">
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">No</th>
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">Tanggal</th>
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">Siswa</th>
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">Kelas</th>
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">Masuk / Keluar</th>
                    <th class="p-5 text-left font-black text-sm tracking-wider uppercase">Status</th>
                    <th class="p-5 text-center font-black text-sm tracking-wider uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody id="attendance-tbody">
                @include('admin.attendances._table')
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $attendances->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolFilter = document.getElementById('school_filter');
    const academicYearFilter = document.getElementById('academic_year_filter');
    const classFilter = document.getElementById('classroom_filter');
    const originalClasses = classFilter ? Array.from(classFilter.options) : [];

    function updateClassFilter() {
        if(!classFilter) return;
        const schoolId = schoolFilter ? schoolFilter.value : '';
        const academicYearId = academicYearFilter ? academicYearFilter.value : '';
        const selectedValue = classFilter.value;
        
        classFilter.innerHTML = '';
        originalClasses.forEach(opt => {
            const matchesSchool = opt.value === "" || opt.getAttribute('data-school-id') === schoolId || schoolId === "";
            const matchesAY = opt.value === "" || opt.getAttribute('data-academic-year-id') === academicYearId || academicYearId === "";
            if(matchesSchool && matchesAY) {
                classFilter.appendChild(opt.cloneNode(true));
            }
        });
        
        // Restore selected value if it exists in the filtered options
        if (Array.from(classFilter.options).some(opt => opt.value === selectedValue)) {
            classFilter.value = selectedValue;
        } else {
            classFilter.value = "";
        }
    }

    if(schoolFilter) {
        schoolFilter.addEventListener('change', updateClassFilter);
    }
    if(academicYearFilter) {
        academicYearFilter.addEventListener('change', updateClassFilter);
    }
    
    // Run initially to filter classes on load
    updateClassFilter();

    // Auto Refresh Table (Every 5 seconds)
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            const tbody = document.getElementById('attendance-tbody');
            if(tbody && html.trim() !== '') {
                tbody.innerHTML = html;
            }
        })
        .catch(error => console.error('Error auto-refreshing table:', error));
    }, 5000);

});
</script>
@endpush