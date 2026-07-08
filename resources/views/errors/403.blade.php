@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('gradient', 'bg-gradient-to-r from-red-500 to-orange-600')

@section('icon')
<svg class="w-24 h-24 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
</svg>
@endsection

@section('heading', 'Akses Ditolak')

@section('description')
Anda tidak memiliki izin untuk mengakses halaman ini.
@if(!empty($message) && $message !== 'Forbidden')
    <br><span class="text-sm text-gray-400">{{ $message }}</span>
@endif
Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
@endsection
