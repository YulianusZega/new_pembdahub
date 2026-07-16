@extends('layouts.admin')
@section('title', 'Tambah Penempatan PKL (Kelompok) - Portal Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header Bar --}}
    <div class="flex items-center gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <a href="{{ route('admin.pkl-alumni.placements.index') }}" class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800">Tambah Penempatan PKL (Bisa Kelompok)</h1>
            <p class="text-xs text-gray-500 mt-0.5">Daftarkan satu atau banyak siswa sekaligus ke instansi DUDI</p>
        </div>
    </div>

    {{-- Error validation alert --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-250 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold space-y-1">
            <p class="font-bold">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('admin.pkl-alumni.placements.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- KOLOM KIRI: INFO DUDI & PERIODE --}}
            <div class="lg:col-span-5 space-y-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h2 class="font-bold text-gray-700 mb-4 border-b pb-2"><i class="fas fa-building text-indigo-500 mr-2"></i> Data Penempatan</h2>
                    
                    <div class="space-y-4">
                        {{-- DUDI --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Mitra DUDI</label>
                            <select name="dudi_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                                <option value="">-- Pilih Mitra DUDI --</option>
                                @foreach($dudis as $dudi)
                                    <option value="{{ $dudi->id }}" {{ old('dudi_id') == $dudi->id ? 'selected' : '' }}>
                                        {{ $dudi->name }}
                                        @if($dudi->school_id) ({{ $dudi->school->name ?? 'SMK' }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Belum ada di daftar? <a href="{{ route('admin.pkl-alumni.dudis.create') }}" class="text-indigo-500 underline" target="_blank">Tambah Master DUDI Baru</a></p>
                        </div>

                        {{-- Shift --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Periode Shift PKL</label>
                            <select name="shift" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                                <option value="">-- Pilih Shift PKL --</option>
                                <option value="Shift A (Juli - Oktober)" {{ old('shift') == 'Shift A (Juli - Oktober)' ? 'selected' : '' }}>Shift A (Juli - Oktober)</option>
                                <option value="Shift B (Oktober - Februari)" {{ old('shift') == 'Shift B (Oktober - Februari)' ? 'selected' : '' }}>Shift B (Oktober - Februari)</option>
                            </select>
                        </div>

                        {{-- Teacher --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Guru Pembimbing Internal</label>
                            <select name="teacher_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                                <option value="">Pilih Guru...</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->user->name ?? '-' }} (NIP: {{ $t->nip ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Academic Year --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tahun Ajaran</label>
                            <select name="academic_year_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $activeYear?->id) == $year->id ? 'selected' : '' }}>
                                        Tahun Ajaran {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Start Date --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Mulai Magang</label>
                                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                            </div>

                            {{-- End Date --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Selesai Magang</label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PILIHAN SISWA --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 h-full flex flex-col">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="font-bold text-gray-700"><i class="fas fa-users text-indigo-500 mr-2"></i> Pilih Siswa (Bisa Lebih Dari Satu)</h2>
                        <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg" id="selected-count">0 Terpilih</span>
                    </div>

                    {{-- Search / Filter box --}}
                    <div class="mb-3">
                        <input type="text" id="search-student" placeholder="Cari nama atau NISN siswa..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                    </div>

                    {{-- Checkbox List --}}
                    <div class="border border-gray-200 rounded-xl flex-1 max-h-[500px] overflow-y-auto bg-gray-50 p-2 space-y-1" id="student-list">
                        @if($students->isEmpty())
                            <div class="text-center text-gray-500 py-8 text-sm">
                                Tidak ada siswa SMK yang belum ditempatkan.
                            </div>
                        @else
                            @foreach($students as $s)
                                <label class="student-item flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="student-checkbox w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ (is_array(old('student_ids')) && in_array($s->id, old('student_ids'))) ? 'checked' : '' }}>
                                        <div>
                                            <div class="font-semibold text-gray-800 text-sm student-name">{{ $s->full_name }}</div>
                                            <div class="text-[11px] text-gray-500">NISN: <span class="student-nisn">{{ $s->nisn ?? '-' }}</span> &bull; {{ $s->school->name ?? '' }}</div>
                                        </div>
                                    </div>
                                    @if($s->classroom)
                                        <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $s->classroom->name }}</div>
                                    @endif
                                </label>
                            @endforeach
                        @endif
                    </div>
                    
                    {{-- Select All Buttons --}}
                    <div class="mt-3 flex gap-2">
                        <button type="button" id="btn-select-all" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">Pilih Semua</button>
                        <button type="button" id="btn-deselect-all" class="text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg hover:bg-rose-100 transition">Batalkan Semua</button>
                    </div>

                </div>
            </div>

        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.pkl-alumni.placements.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                Batal
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Penempatan Kelompok
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-student');
        const studentItems = document.querySelectorAll('.student-item');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const countDisplay = document.getElementById('selected-count');
        
        function updateCount() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            countDisplay.innerText = checkedCount + ' Terpilih';
            
            if(checkedCount > 0) {
                countDisplay.classList.remove('bg-gray-100', 'text-gray-500');
                countDisplay.classList.add('bg-indigo-100', 'text-indigo-700');
            } else {
                countDisplay.classList.add('bg-gray-100', 'text-gray-500');
                countDisplay.classList.remove('bg-indigo-100', 'text-indigo-700');
            }
        }
        
        // Initial count
        updateCount();
        
        // Listen to checkbox changes
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateCount);
        });
        
        // Search filter
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                
                studentItems.forEach(item => {
                    const name = item.querySelector('.student-name').innerText.toLowerCase();
                    const nisn = item.querySelector('.student-nisn').innerText.toLowerCase();
                    
                    if(name.includes(term) || nisn.includes(term)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        // Select All
        document.getElementById('btn-select-all').addEventListener('click', function() {
            // Only check visible ones
            studentItems.forEach(item => {
                if(item.style.display !== 'none') {
                    item.querySelector('.student-checkbox').checked = true;
                }
            });
            updateCount();
        });
        
        // Deselect All
        document.getElementById('btn-deselect-all').addEventListener('click', function() {
            // Only uncheck visible ones
            studentItems.forEach(item => {
                if(item.style.display !== 'none') {
                    item.querySelector('.student-checkbox').checked = false;
                }
            });
            updateCount();
        });
    });
</script>
@endpush
@endsection
