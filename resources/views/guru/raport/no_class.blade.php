@extends('layouts.guru')
@section('title', 'Raport Kelas - Portal Guru')

@section('content')
<div class="max-w-xl mx-auto py-12 px-4">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center space-y-6">
        <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-rose-500 shadow-inner">
            <i class="fas fa-file-alt text-3xl"></i>
        </div>
        
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-gray-800">Raport Digital</h1>
            <p class="text-gray-500 max-w-sm mx-auto text-sm">
                Belum ada kelas yang diwalikan untuk Anda pada Tahun Ajaran Aktif ini.
            </p>
        </div>

        <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-4 text-xs text-rose-800 text-left flex items-start gap-3">
            <i class="fas fa-info-circle mt-0.5 text-rose-500 flex-shrink-0"></i>
            <div>
                <span class="font-semibold">Informasi:</span> Menu ini hanya dapat diakses oleh guru yang ditugaskan sebagai Wali Kelas. Jika ini adalah kesalahan, silakan hubungi Administrator Sekolah untuk penugasan Wali Kelas.
            </div>
        </div>

        <div>
            <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white rounded-xl text-sm font-semibold shadow-md transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
