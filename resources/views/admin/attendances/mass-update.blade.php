@extends('layouts.admin')
@section('title', 'Mass Update Absensi')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-layer-group text-3xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mass Update Absensi</h1>
                <p class="text-gray-600">Tetapkan absensi siswa secara massal untuk kejadian khusus</p>
            </div>
        </div>
        <a href="{{ route('admin.attendances.index') }}" class="text-gray-500 hover:text-gray-700 font-medium btn btn-light flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-sliders text-yellow-500"></i> Setup Mass Update
            </h2>
            <p class="text-sm text-gray-500 mt-1">Status yang dipilih akan diterapkan secara bersamaan pada seluruh siswa di target yang ditentukan.</p>
        </div>

        <form action="{{ route('admin.attendances.mass-update.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" required
                                class="pl-10 w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-gray-50 py-2.5">
                        </div>
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}" required
                                class="pl-10 w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-gray-50 py-2.5">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Biarkan sama dengan Tanggal Mulai jika hanya update 1 hari.</p>
                    </div>

                    <div class="flex items-center gap-2 mt-4 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <input type="checkbox" name="exclude_weekends" id="exclude_weekends" value="1" class="w-5 h-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500" checked>
                        <label for="exclude_weekends" class="text-sm font-medium text-gray-700 cursor-pointer">Abaikan Hari Sabtu & Minggu</label>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Pilih Status Absensi</label>
                        <select name="status" id="status" required
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-gray-50 py-2.5">
                            <option value="">-- Pilih Status --</option>
                            <option value="libur" class="font-medium text-blue-600">Libur Nasional / Kejepit</option>
                            <option value="pulang" class="font-medium text-orange-600">Dipulangkan Lebih Awal</option>
                            <option value="hadir" class="font-medium text-green-600">Hadir Sempurna</option>
                            <option value="terlambat" class="font-medium text-orange-600">Hadir Terlambat</option>
                            <option value="alpha" class="font-medium text-red-600">Alpha / Tidak Hadir (Semua)</option>
                            <option value="reset" class="font-medium text-gray-700 bg-gray-100">🚫 Reset / Hapus Daftar Hadir</option>
                        </select>
                    </div>

                    <div id="reset_warning" class="hidden animate-pulse bg-red-100 border-2 border-red-500 p-4 rounded-xl">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-red-800">PERINGATAN KERAS!</h4>
                                <p class="text-xs text-red-700 leading-relaxed">
                                    Opsi <b>RESET</b> akan menghapus seluruh catatan absensi pada tanggal & target terpilih. Data yang dihapus tidak dapat dikembalikan. Gunakan hanya jika ada kesalahan input/uji coba.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6 bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Target Pemberlakuan</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-white transition bg-white shadow-sm">
                                <input type="radio" name="target_type" value="school" class="target-type text-yellow-500 focus:ring-yellow-500 h-4 w-4" checked>
                                <span class="ml-3 font-medium text-gray-700">Skala Unit Sekolah (Satu Gedung)</span>
                            </label>
                            
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-white transition bg-gray-50">
                                <input type="radio" name="target_type" value="classroom" class="target-type text-yellow-500 focus:ring-yellow-500 h-4 w-4">
                                <span class="ml-3 font-medium text-gray-700">Skala Rombongan Belajar (Satu Kelas)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Target School -->
                    <div id="schoolSelectGroup" class="pt-2 transition-all duration-300">
                        <label for="school_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Unit Sekolah</label>
                        @if($isSuperAdmin)
                        <select name="school_id" id="school_id_select" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-white py-2.5">
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-center text-yellow-500">
                                <i class="fas fa-school text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Terkunci Pada Unit Utama</p>
                                <p class="text-base font-bold text-gray-800">{{ $schools->first()->name ?? 'Sekolah Anda' }}</p>
                            </div>
                            <!-- Hidden input just to satisfy logic if it ever needs to read school_id, but the backend uses auth()->user()->school_id anyway -->
                        </div>
                        @endif
                    </div>

                    <!-- Target Classroom -->
                    <div id="classroomSelectGroup" class="pt-2 hidden transition-all duration-300">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Rombongan Belajar</label>
                        <select name="classroom_id" id="classroom_id" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-white py-2.5">
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Keterangan / Alasan Tambahan (Opsional)</label>
                <textarea name="notes" id="notes" rows="3" 
                    placeholder="Contoh: Libur nasional hari buruh, dipulangkan karena area sekolah banjir..." 
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-gray-50 p-4"></textarea>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.attendances.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Batalkan
                </a>
                <button type="submit" onclick="return confirm('Peringatan: Aksi ini akan menimpa seluruh data absensi siswa di target yang Anda pilih pada tanggal tersebut. Lanjutkan?')" 
                    class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-xl font-bold shadow-lg hover:from-yellow-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fas fa-bolt"></i> Eksekusi Mass Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const targetTypeRadios = document.querySelectorAll('.target-type');
        const schoolGroup = document.getElementById('schoolSelectGroup');
        const classGroup = document.getElementById('classroomSelectGroup');
        const selectSchool = document.getElementById('school_id_select');
        const selectClass = document.getElementById('classroom_id');

        targetTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove visual highlighting
                document.querySelectorAll('.target-type').forEach(r => {
                    r.closest('label').classList.remove('bg-white', 'shadow-sm');
                    r.closest('label').classList.add('bg-gray-50');
                });
                
                // Add visual highlighting to selected
                this.closest('label').classList.add('bg-white', 'shadow-sm');
                this.closest('label').classList.remove('bg-gray-50');

                if (this.value === 'school') {
                    schoolGroup.classList.remove('hidden');
                    classGroup.classList.add('hidden');
                    if(selectSchool) selectSchool.setAttribute('required', 'required');
                    if(selectClass) selectClass.removeAttribute('required');
                } else {
                    schoolGroup.classList.add('hidden');
                    classGroup.classList.remove('hidden');
                    if(selectSchool) selectSchool.removeAttribute('required');
                    if(selectClass) selectClass.setAttribute('required', 'required');
                }
            });
        });
        
        // Trigger initial state
        const initialChecked = document.querySelector('.target-type:checked');
        if(initialChecked) initialChecked.dispatchEvent(new Event('change'));
        // Toggle Reset Warning
    const statusSelect = document.getElementById('status');
    const resetWarning = document.getElementById('reset_warning');
    
    if(statusSelect && resetWarning) {
        statusSelect.addEventListener('change', function() {
            if(this.value === 'reset') {
                resetWarning.classList.remove('hidden');
            } else {
                resetWarning.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
