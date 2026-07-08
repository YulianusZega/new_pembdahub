@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('gradient', 'bg-gradient-to-r from-orange-500 to-red-600')

@section('icon')
<svg class="w-24 h-24 mx-auto text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('heading', 'Terlalu Banyak Permintaan')

@section('description')
Anda telah mengirim terlalu banyak permintaan dalam waktu singkat.
Silakan tunggu beberapa saat sebelum mencoba lagi.
@endsection
