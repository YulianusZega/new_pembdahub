@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Sekolah Baru</h1>
                <p class="text-gray-600 mt-1">Lengkapi informasi sekolah di bawah ini</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-1">Terdapat beberapa kesalahan:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.schools.store') }}" method="POST">
        @csrf
        
        <!-- Informasi Dasar -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Dasar</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            Nama Sekolah <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: SMA Negeri 1 Jakarta" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-clipboard mr-1"></i> Tipe Sekolah <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                        <option value="">Pilih Tipe</option>
                        <option value="SMP" {{ old('type') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ old('type') == 'SMA' ? 'selected' : '' }}>SMA</option>
                        <option value="SMK" {{ old('type') == 'SMK' ? 'selected' : '' }}>SMK</option>
                        <option value="yayasan" {{ old('type') == 'yayasan' ? 'selected' : '' }}>Yayasan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-list-ol mr-1"></i> NPSN
                        </span>
                    </label>
                    <input type="text" name="npsn" value="{{ old('npsn') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="Nomor Pokok Sekolah Nasional">
                </div>
            </div>
        </div>

        <!-- Alamat -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-sm">
                    2
                </div>
                <h2 class="text-xl font-bold text-gray-900">Alamat & Lokasi</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt mr-1"></i> Alamat Lengkap
                        </span>
                    </label>
                    <textarea name="address" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="Jalan, nomor, kelurahan, kecamatan...">{{ old('address') }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-2"><i class="fas fa-city mr-1"></i> Kota/Kabupaten</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            placeholder="Contoh: Jakarta">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-2"><i class="fas fa-map mr-1"></i> Provinsi</span>
                        </label>
                        <input type="text" name="province" value="{{ old('province') }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            placeholder="Contoh: DKI Jakarta">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-mailbox mr-1"></i> Kode Pos</span>
                    </label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: 12345">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="md:col-span-2">
                        <p class="text-xs font-bold text-blue-700 uppercase tracking-wider flex items-center gap-2 mb-1">
                            <i class="fas fa-satellite"></i> Koordinat GPS (Untuk Absensi Mandiri)
                        </p>
                        <p class="text-xs text-blue-600 mb-2 italic">Masukkan koordinat sekolah agar siswa bisa melakukan absensi via GPS.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude') }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            placeholder="Contoh: -1.23456789">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude') }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            placeholder="Contoh: 116.12345678">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white font-bold text-sm">
                    3
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Kontak</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-phone mr-1"></i> Telepon</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: 021-12345678">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-envelope mr-1"></i> Email</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="email@sekolah.com">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-globe mr-1"></i> Website</span>
                    </label>
                    <input type="text" name="website" value="{{ old('website') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                        placeholder="https://www.sekolah.com">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2"><i class="fas fa-user mr-1"></i> Kepala Sekolah</span>
                    </label>
                    <select name="principal_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">-- Pilih Kepala Sekolah --</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('principal_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->teacher_code }} - {{ $teacher->full_name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Pilih dari guru yang sudah ditugaskan sebagai Kepala Sekolah. Jika memilih guru tanpa jabatan, sistem akan otomatis menugaskan sebagai Kepala Sekolah.</p>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-yellow-500 to-yellow-600 text-white font-bold text-sm">
                    4
                </div>
                <h2 class="text-xl font-bold text-gray-900">Status Sekolah</h2>
            </div>
            
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Sekolah Aktif</span>
                </label>
                <span class="text-sm text-gray-500">Status sekolah dalam sistem</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 justify-end bg-white rounded-2xl shadow-lg p-6">
            <a href="{{ route('admin.schools.index') }}" 
                class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </span>
            </a>
            <button type="submit" 
                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold text-sm hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Sekolah
                </span>
            </button>
        </div>
    </form>
</div>
@endsection