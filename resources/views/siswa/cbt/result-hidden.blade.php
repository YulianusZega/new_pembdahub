@extends('layouts.siswa')
@section('title', 'Hasil Belum Tersedia')
@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="relative">
        <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 flex items-center justify-center mx-auto mb-6 shadow-sm border border-amber-100">
            <i class="fas fa-hourglass-half text-5xl text-amber-500"></i>
        </div>
        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-amber-400 animate-pulse" style="margin-left: calc(50% + 30px)"></div>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-3">Hasil Belum Tersedia</h1>
    <p class="text-gray-600 mb-2 text-sm">Ujian <strong class="text-gray-900">{{ $exam->exam_title }}</strong> telah selesai dikerjakan.</p>
    <p class="text-gray-500 mb-8 text-sm">Guru belum mengizinkan hasil ujian ditampilkan. Silakan hubungi guru Anda untuk informasi lebih lanjut.</p>
    <a href="{{ route('siswa.cbt.index') }}" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:shadow-lg transition font-bold text-sm gap-2">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar Ujian
    </a>
</div>
@endsection
