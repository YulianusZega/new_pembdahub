@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')
@section('gradient', 'bg-gradient-to-r from-indigo-500 to-blue-600')

@section('icon')
<svg class="w-24 h-24 mx-auto text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('heading', 'Halaman Tidak Ditemukan')

@section('description')
Halaman yang Anda cari tidak ada atau telah dipindahkan.
Periksa kembali URL yang Anda masukkan atau kembali ke beranda.
@endsection
