@extends('layouts.treasurer')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 shadow-sm rounded-t-2xl px-6 py-8 border-b border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-emerald-900">Detail Pembayaran</h1>
                    <p class="text-emerald-700 mt-1">{{ $payment->receipt_number }}</p>
                </div>
                @if($payment->is_verified)
                    <span class="px-4 py-2 bg-green-500 text-white rounded-xl font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terverifikasi
                    </span>
                @else
                    <span class="px-4 py-2 bg-yellow-500 text-white rounded-xl font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Menunggu Verifikasi
                    </span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-sm rounded-b-2xl px-6 py-8 space-y-8">
            <!-- Payment Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Informasi Pembayaran
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <p class="text-sm text-gray-600">Jumlah Pembayaran</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">
                            Rp {{ number_format($payment->amount_paid ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600">Tanggal Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">
                            {{ $payment->payment_date->format('d M Y') }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600">Metode Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">
                            @if($payment->payment_method == 'cash')
                                💵 Tunai
                            @elseif($payment->payment_method == 'transfer')
                                🏦 Transfer Bank
                            @elseif($payment->payment_method == 'qris')
                                📱 QRIS
                            @elseif($payment->payment_method == 'card')
                                💳 Kartu
                            @elseif($payment->payment_method == 'check')
                                📝 Cek
                            @else
                                {{ ucfirst($payment->payment_method ?? '-') }}
                            @endif
                        </p>
                    </div>
                    @if($payment->reference_number)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600">Nomor Referensi</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">
                            {{ $payment->reference_number }}
                        </p>
                    </div>
                    @endif
                    @if($payment->payment_method == 'qris' && $payment->qris_transaction_id)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600">ID Transaksi QRIS</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">
                            {{ $payment->qris_transaction_id }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Student Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informasi Siswa
                </h2>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->student->full_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">NISN</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->student->nisn ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informasi Tagihan
                </h2>
                @if($payment->bill_id && $payment->bill)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Jenis Tagihan</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->bill->paymentType->type_name ?? '-' }} ({{ $payment->bill->academicYear->year }})</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Periode</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @if($payment->bill->month)
                                    {{ $months[$payment->bill->month] ?? $payment->bill->month }} {{ $payment->bill->year ?? '' }}
                                @else
                                    {{ $payment->bill->year ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Tagihan</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($payment->bill->amount ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status Tagihan</p>
                            <p class="text-lg font-semibold">
                                @if($payment->bill->status == 'lunas')
                                    <span class="text-green-600">✅ Lunas</span>
                                @elseif($payment->bill->status == 'cicilan')
                                    <span class="text-yellow-600">⏳ Cicilan</span>
                                @else
                                    <span class="text-red-600">❌ Belum Bayar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                    <p class="text-gray-700">
                        <span class="font-semibold">⚠️ Pembayaran Manual</span><br>
                        Pembayaran ini tidak terkait dengan tagihan tertentu. Mungkin merupakan pembayaran langsung atau batch payment.
                    </p>
                </div>
                @endif
            </div>

            <!-- Verification Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Verifikasi
                </h2>
                @if($payment->is_verified)
                <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($payment->processed_by)
                        <div>
                            <p class="text-sm text-gray-600">Diproses Oleh</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->processedBy->name ?? 'Bendahara' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Bendahara</p>
                        </div>
                        @endif
                        @if($payment->verified_by)
                        <div>
                            <p class="text-sm text-gray-600">Diverifikasi Oleh</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->verifiedBy->name ?? 'Admin' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Super Admin</p>
                        </div>
                        @endif
                        @if($payment->verified_at)
                        <div>
                            <p class="text-sm text-gray-600">Waktu Verifikasi</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->verified_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($payment->processed_by)
                        <div>
                            <p class="text-sm text-gray-600">Diproses Oleh</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->processedBy->name ?? 'Bendahara' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Bendahara</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Status Verifikasi</p>
                            <p class="text-lg font-semibold text-yellow-600">
                                ⏳ Menunggu Verifikasi Admin
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notes -->
            @if($payment->notes)
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Catatan
                </h2>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-gray-700">{{ $payment->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Dibuat:</span> {{ $payment->created_at->format('d M Y, H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Terakhir Diubah:</span> {{ $payment->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between gap-4">
            <a href="{{ route('treasurer.payments.index') }}" 
                class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <div class="flex gap-2">
                <a href="{{ route('treasurer.payments.receipt', $payment) }}" 
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Kwitansi
                </a>
                <button onclick="window.print()" 
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-xl font-medium hover:bg-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .container {
        max-width: 100%;
    }
    button, a[href*="edit"], a[href*="back"] {
        display: none !important;
    }
}
</style>
@endsection
