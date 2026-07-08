@extends('layouts.admin')

@section('title', 'Tambah Jurusan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Jurusan</h1>
                <p class="text-gray-600 mt-1">Buat jurusan baru untuk sekolah</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-2">Terdapat kesalahan pada form:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.majors.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Jurusan</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                    <select name="school_id" id="school_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($schools as $s)
                        <option value="{{ $s->id }}" data-level="{{ strtoupper($s->level) }}" {{ old('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ strtoupper($s->level) }})</option>
                        @endforeach
                    </select>
                    @error('school_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Info Penjelasan Dinamis -->
                <div id="info-box" class="hidden p-4 rounded-xl border-l-4">
                    <div id="info-sma" class="hidden">
                        <p class="text-sm text-blue-700">
                            <strong><i class="fas fa-info-circle text-blue-500 mr-1"></i> SMA/SMP:</strong> Gunakan form ini untuk menambah Jurusan (contoh: IPA, IPS, Bahasa).
                        </p>
                    </div>
                    <div id="info-smk" class="hidden">
                        <p class="text-sm text-orange-700">
                            <strong><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> SMK:</strong> Untuk SMK, sebaiknya gunakan <strong>Tab "Program & Konsentrasi Keahlian SMK"</strong> di halaman Jurusan.<br>
                            Atau Anda dapat langsung ke: <a href="{{ route('admin.program-keahlians.create') }}" class="underline font-semibold">Tambah Program Keahlian</a>
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-font mr-1"></i> Kode Jurusan</label>
                    <input type="text" name="major_code" value="{{ old('major_code') }}" 
                        placeholder="Contoh: IPA, IPS, TKJ"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                    @error('major_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Nama Jurusan</label>
                    <input type="text" name="major_name" value="{{ old('major_name') }}" 
                        placeholder="Contoh: Ilmu Pengetahuan Alam"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                    @error('major_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-edit mr-1"></i> Keterangan</label>
                    <textarea name="description" rows="3"
                        placeholder="Deskripsi jurusan (opsional)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-check-circle text-green-500 mr-1"></i> Jurusan aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-700 text-white rounded-xl font-medium hover:from-amber-700 hover:to-orange-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Jurusan
            </button>
            <a href="{{ route('admin.majors.index') }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolSelect = document.getElementById('school_id');
    const infoBox = document.getElementById('info-box');
    const infoSMA = document.getElementById('info-sma');
    const infoSMK = document.getElementById('info-smk');
    
    schoolSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const schoolLevel = selectedOption.getAttribute('data-level');
        
        // Reset
        infoBox.classList.add('hidden');
        infoSMA.classList.add('hidden');
        infoSMK.classList.add('hidden');
        
        if (schoolLevel === 'SMK') {
            // Tampilkan warning untuk SMK
            infoBox.classList.remove('hidden');
            infoBox.classList.add('bg-orange-50', 'border-orange-500');
            infoBox.classList.remove('bg-blue-50', 'border-blue-500');
            infoSMK.classList.remove('hidden');
        } else if (schoolLevel === 'SMA' || schoolLevel === 'SMP') {
            // Tampilkan info untuk SMA/SMP
            infoBox.classList.remove('hidden');
            infoBox.classList.add('bg-blue-50', 'border-blue-500');
            infoBox.classList.remove('bg-orange-50', 'border-orange-500');
            infoSMA.classList.remove('hidden');
        }
    });
    
    // Trigger on page load if old value exists
    if (schoolSelect.value) {
        schoolSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection