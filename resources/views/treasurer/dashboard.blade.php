@extends('layouts.treasurer')

@section('title', 'Dashboard Bendahara')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Bendahara</h1>
                <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}! | {{ auth()->user()->school->school_name ?? 'Sekolah' }}</p>
                @if($currentAcademicYear)
                <p class="text-sm text-green-600 mt-1 font-medium">📅 Tahun Ajaran: {{ $currentAcademicYear->year }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Bulan Ini - Tunggakan -->
        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-orange-100 text-xs mb-1">TUNGGAKAN BULAN INI</p>
                    <p class="text-orange-50 text-xs mb-2">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['bills_this_month']) }}</h3>
                    <p class="text-orange-100 text-sm">Rp {{ number_format($stats['outstanding_this_month'], 0, ',', '.') }}</p>
                </div>
                <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Bulan Ini - Terbayar -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-green-100 text-xs mb-1">TERBAYAR BULAN INI</p>
                    <p class="text-green-50 text-xs mb-2">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                    <h3 class="text-3xl font-bold mb-1">Rp {{ number_format($stats['paid_this_month'], 0, ',', '.') }}</h3>
                    <p class="text-green-100 text-sm">{{ number_format($stats['payments_today']) }} pembayaran hari ini</p>
                </div>
                <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Sampai Bulan Ini - Tunggakan -->
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-purple-100 text-xs mb-1">TUNGGAKAN S.D. BULAN INI</p>
                    <p class="text-purple-50 text-xs mb-2">Jan - {{ \Carbon\Carbon::now()->format('M Y') }}</p>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['bills_ytd']) }}</h3>
                    <p class="text-purple-100 text-sm">Rp {{ number_format($stats['outstanding_ytd'], 0, ',', '.') }}</p>
                </div>
                <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <!-- Sampai Bulan Ini - Terbayar -->
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-xs mb-1">TERBAYAR S.D. BULAN INI</p>
                    <p class="text-blue-50 text-xs mb-2">Jan - {{ \Carbon\Carbon::now()->format('M Y') }}</p>
                    <h3 class="text-3xl font-bold mb-1">Rp {{ number_format($stats['paid_ytd'], 0, ',', '.') }}</h3>
                    <p class="text-blue-100 text-sm">{{ number_format($stats['total_students']) }} siswa aktif</p>
                </div>
                <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <a href="{{ route('treasurer.bills.create') }}" class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all border-2 border-transparent hover:border-blue-500">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Buat Tagihan</h4>
                    <p class="text-xs text-gray-500">Tagihan baru</p>
                </div>
            </div>
        </a>

        <a href="{{ route('treasurer.bills.bulk-create') }}" class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all border-2 border-transparent hover:border-purple-500">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Tagihan Massal</h4>
                    <p class="text-xs text-gray-500">Per kelas</p>
                </div>
            </div>
        </a>

        <a href="{{ route('treasurer.payments.create') }}" class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all border-2 border-transparent hover:border-green-500">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Input Pembayaran</h4>
                    <p class="text-xs text-gray-500">Catat pembayaran</p>
                </div>
            </div>
        </a>

        <a href="{{ route('treasurer.payments.bulk-create') }}" class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all border-2 border-transparent hover:border-orange-500">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-orange-100 text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Pembayaran Massal</h4>
                    <p class="text-xs text-gray-500">Per kelas</p>
                </div>
            </div>
        </a>

        <a href="{{ route('treasurer.reports.index') }}" class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all border-2 border-transparent hover:border-indigo-500">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Laporan</h4>
                    <p class="text-xs text-gray-500">Progress & Export</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Pembayaran Terbaru</h2>
                <a href="{{ route('treasurer.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>

            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                <div class="border border-gray-200 rounded-xl p-3 hover:border-blue-300 transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">{{ $payment->student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->bill->paymentType->type_name ?? 'Umum' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $payment->payment_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</p>
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-700' : '' }} {{ $payment->payment_method === 'transfer' ? 'bg-blue-100 text-blue-700' : '' }} {{ $payment->payment_method === 'qris' ? 'bg-purple-100 text-purple-700' : '' }}">
                                {{ strtoupper($payment->payment_method) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Belum ada pembayaran hari ini</p>
                @endforelse
            </div>
        </div>

        <!-- Unpaid Bills -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Tagihan Belum Lunas</h2>
                <a href="{{ route('treasurer.bills.index', ['status' => 'belum_bayar']) }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>

            <div class="space-y-3">
                @forelse($unpaidBills->take(8) as $bill)
                <div class="border border-gray-200 rounded-xl p-3 hover:border-orange-300 transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm">{{ $bill->student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $bill->paymentType->type_name }}</p>
                            @if($bill->month)
                            <p class="text-xs text-gray-400 mt-1">Bulan: {{ date('F', mktime(0, 0, 0, $bill->month, 1)) }} {{ $bill->year }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-orange-600">Rp {{ number_format($bill->outstanding_with_late_fee, 0, ',', '.') }}</p>
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $bill->status === 'belum_bayar' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $bill->status === 'belum_bayar' ? 'Belum Bayar' : 'Cicilan' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Semua tagihan sudah lunas! 🎉</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
