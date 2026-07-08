@extends('errors.layout')

@section('title', '419 - Sesi Berakhir')
@section('code', '419')
@section('gradient', 'bg-gradient-to-r from-yellow-500 to-amber-600')

@section('icon')
<svg class="w-24 h-24 mx-auto text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('heading', 'Sesi Berakhir')

@section('description')
Sesi Anda telah berakhir karena tidak aktif terlalu lama.
Silakan muat ulang halaman dan coba lagi.
@endsection
