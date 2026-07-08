@extends('layouts.admin')

@section('title', 'Pengaturan Komponen PSB')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/3"></div>
        <div class="relative">
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <i class="fas fa-school-flag"></i> Pusat Kendali PSB Unit
            </h1>
            <p class="text-blue-100 mt-1">Konfigurasi mandiri untuk setiap unit sekolah dalam naungan Yayasan.</p>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl flex items-start gap-3">
        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-amber-800 uppercase tracking-wider mb-1">Informasi</p>
            <p class="text-xs text-amber-700 leading-relaxed">
                Setiap unit sekolah memiliki pengaturan PSB yang berbeda. Anda dapat mengatur komponen biaya, berkas persyaratan, 
                hingga informasi publik yang akan ditampilkan di halaman pendaftaran masing-masing sekolah.
            </p>
        </div>
    </div>

    {{-- Schools Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($schools as $school)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-blue-100 transition-all group flex flex-col h-full">
            <div class="p-6 flex-1">
                <div class="flex items-start justify-between mb-4">
                    @php
                        $colorClass = match(strtoupper($school->type)) {
                            'SMP' => 'from-blue-500 to-cyan-500',
                            'SMA' => 'from-purple-500 to-indigo-500',
                            'SMK' => 'from-orange-500 to-red-500',
                            default => 'from-gray-500 to-gray-600'
                        };
                    @endphp
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $colorClass }} text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-blue-100">
                        {{ substr($school->name, 0, 1) }}
                    </div>
                    <span class="px-3 py-1.5 {{ $school->psb_is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-400 border border-gray-100' }} rounded-xl text-[10px] font-bold uppercase tracking-widest">
                        {{ $school->psb_is_active ? '● PSB Aktif' : '○ PSB Tutup' }}
                    </span>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $school->name }}</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $school->type }} • {{ $school->city }}</p>
                </div>

                <div class="space-y-3 pt-4 border-t border-dashed border-gray-100 mb-6">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Gelombang Pendaftaran:</span>
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md font-bold">{{ $school->registration_waves_count }} Wave</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Metode Seleksi:</span>
                        <span class="px-2 py-0.5 {{ $school->requires_test ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }} rounded-md font-bold">
                            {{ $school->requires_test ? 'Tes Masuk' : 'Berkas & Raport' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Berkas Wajib:</span>
                        <span class="font-bold text-slate-800">
                            {{ is_array($school->psb_required_documents) ? count($school->psb_required_documents) : 0 }} Dokumen
                        </span>
                    </div>
                </div>

                <a href="{{ route('admin.psb.settings.edit', $school->id) }}" class="w-full py-4 bg-slate-50 group-hover:bg-blue-600 group-hover:text-white text-slate-700 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-3 shadow-sm active:scale-95">
                    <i class="fas fa-sliders-h"></i> KONFIGURASI PSB
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Help Section --}}
    <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
        <div class="max-w-2xl mx-auto">
            <h4 class="text-xl font-bold text-gray-900 mb-2">Butuh Bantuan?</h4>
            <p class="text-sm text-gray-500 mb-6">
                Jika Anda menemukan kesulitan dalam melakukan sinkronisasi data antar unit, silakan hubungi Tim IT Yayasan atau baca dokumentasi sistem PSB.
            </p>
            <div class="flex justify-center gap-3">
                <a href="#" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-200 transition">Panduan Sistem</a>
                <a href="#" class="px-6 py-3 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-blue-100 transition">Hubungi IT</a>
            </div>
        </div>
    </div>
</div>
@endsection
