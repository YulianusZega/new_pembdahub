@extends('layouts.admin')

@section('title', 'Konten Umum Homepage')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                <i class="fas fa-cog text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Konten Umum Homepage</h1>
                <p class="text-gray-600 mt-1">Kelola statistik, sambutan, dan informasi pendaftaran</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.homepage-content.update') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Statistik -->
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-amber-500"></i> Statistik Yayasan
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Berdiri</label>
                        <input type="text" name="stat_tahun" value="{{ $settings['stat_tahun'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Siswa</label>
                        <input type="text" name="stat_siswa" value="{{ $settings['stat_siswa'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Sekolah</label>
                        <input type="text" name="stat_unit" value="{{ $settings['stat_unit'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Program Keahlian</label>
                        <input type="text" name="stat_program" value="{{ $settings['stat_program'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <!-- PSB / Pendaftaran -->
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-plus text-emerald-500"></i> Info Pendaftaran (PSB)
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Pelajaran</label>
                        <input type="text" name="psb_tp" value="{{ $settings['psb_tp'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                        <p class="text-[10px] text-gray-400 mt-1">Contoh: 2026/2027</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Periode Pendaftaran</label>
                        <input type="text" name="psb_periode" value="{{ $settings['psb_periode'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                        <p class="text-[10px] text-gray-400 mt-1">Contoh: 1 Feb – 30 Jun 2026</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Label Status</label>
                        <input type="text" name="psb_status" value="{{ $settings['psb_status'] }}" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                        <p class="text-[10px] text-gray-400 mt-1">Contoh: Dibuka / Segera Hadir / Ditutup</p>
                    </div>
                </div>
            </div>

            <!-- Sambutan Ketua -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 space-y-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-quote-left text-rose-500"></i> Sambutan Ketua Yayasan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Ketua</label>
                            <input type="text" name="ketua_nama" value="{{ $settings['ketua_nama'] }}" 
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan</label>
                            <input type="text" name="ketua_jabatan" value="{{ $settings['ketua_jabatan'] }}" 
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Sambutan (Kutipan)</label>
                        <textarea name="ketua_quote" rows="4" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500">{{ $settings['ketua_quote'] }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-gray-800 to-gray-900 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
