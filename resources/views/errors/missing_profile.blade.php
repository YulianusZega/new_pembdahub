@extends('errors.layout')

@section('title', 'Profil Belum Lengkap')
@section('code', '404')
@section('gradient', 'bg-gradient-to-r from-orange-400 to-amber-500')

@section('icon')
<svg class="w-24 h-24 mx-auto text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('heading', 'Data Profil Tidak Ditemukan')

@section('description')
Sistem mendeteksi bahwa Anda telah *login* dan memiliki akses sebagai <strong>{{ $role ?? 'Pengguna' }}</strong>, namun akun Anda saat ini <strong>belum ditautkan ke data profil resmi</strong>. <br><br>
Kondisi ini dicegah oleh sistem agar tidak menimbulkan <i>error</i> berkepanjangan pada <i>dashboard</i>. Silakan hubungi <strong>Administrator Sekolah</strong> untuk melengkapi penugasan dan mengaitkan akun Anda dengan data profil yang valid.
@endsection

@section('actions')
<form method="POST" action="{{ route('logout') }}" class="inline">
    @csrf
    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Keluar / Logout
    </button>
</form>
@endsection
