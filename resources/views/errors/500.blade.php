@extends('errors.layout')

@section('title', '500 - Kesalahan Server')
@section('code', '500')
@section('gradient', 'bg-gradient-to-r from-red-600 to-rose-700')

@section('icon')
<svg class="w-24 h-24 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('heading', 'Kesalahan Server Internal')

@section('description')
Terjadi kesalahan pada server. Tim teknis kami telah diberitahu
dan sedang bekerja untuk memperbaiki masalah ini.
Silakan coba lagi dalam beberapa menit.
@endsection
