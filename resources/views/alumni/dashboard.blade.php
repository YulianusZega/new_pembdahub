@extends('layouts.alumni')

@section('title', 'Dashboard Alumni')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Left Sidebar / Profil -->
        <div class="w-full md:w-1/3 lg:w-1/4">
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center border border-gray-100">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    <img src="{{ $alumni->photo_url }}" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover shadow-sm border-2 border-indigo-100">
                    @if($alumni->is_approved)
                        <div class="absolute bottom-0 right-0 bg-green-500 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-sm" title="Verified Alumni">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $alumni->full_name }}</h3>
                <p class="text-sm text-indigo-600 font-medium mb-1">Angkatan {{ $alumni->graduation_year }}</p>
                @if($alumni->school)
                    <p class="text-xs text-gray-500">{{ $alumni->school->name }}</p>
                @endif
                
                <div class="mt-6 pt-6 border-t border-gray-100 text-left space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Pekerjaan</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $alumni->occupation ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Instansi/Perusahaan</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $alumni->company_name ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Content -->
        <div class="w-full md:w-2/3 lg:w-3/4 space-y-6">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-lg p-6 sm:p-8 text-white relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="absolute right-20 -bottom-10 w-32 h-32 bg-cyan-400 opacity-20 rounded-full blur-xl"></div>
                
                <div class="relative z-10">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-2">Selamat Datang di Rembuk Alumni!</h2>
                    <p class="text-indigo-100 text-sm sm:text-base max-w-2xl">
                        Senang bisa melihat Anda kembali. Ruang ini didedikasikan untuk Anda, para alumni Perguruan PEMBDA, agar dapat terus terhubung, berbagi inspirasi, dan berkontribusi bagi almamater.
                    </p>
                    
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-50 transition shadow-sm">
                            <i class="fas fa-comments"></i> Buka Pembda Space
                        </a>
                        <a href="{{ route('alumni.tracer.form') }}" class="inline-flex items-center gap-2 bg-indigo-500 bg-opacity-30 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-opacity-40 transition border border-indigo-400 border-opacity-30">
                            <i class="fas fa-briefcase"></i> Isi Tracer Study
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Jejaring Alumni</h4>
                        <p class="text-sm text-gray-500">Temukan rekan seangkatan Anda dan perluas relasi profesional melalui Pembda Space.</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
                        <i class="fas fa-hand-holding-heart text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Kontribusi & Donasi</h4>
                        <p class="text-sm text-gray-500">Segera Hadir. Fitur crowdfunding untuk beasiswa dan pengembangan fasilitas sekolah.</p>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>
@endsection
