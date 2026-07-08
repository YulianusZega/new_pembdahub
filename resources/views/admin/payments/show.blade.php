@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pembayaran</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap pembayaran</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">No. Kwitansi</p>
                    <p class="text-xl font-bold text-gray-900">{{ $payment->receipt_number ?? 'N/A' }}</p>
                </div>
                @if($payment->is_verified)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Terverifikasi
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Belum Terverifikasi
                </span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Payment Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Informasi Pembayaran
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">Jumlah Dibayar</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">Tanggal Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $payment->payment_date->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">Metode Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @php
                                $methods = [
                                    'cash' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[13px] font-bold border border-emerald-100"><i class="fas fa-money-bill"></i> Tunai</span>',
                                    'transfer' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[13px] font-bold border border-blue-100"><i class="fas fa-university"></i> Transfer Bank</span>',
                                    'qris' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-[13px] font-bold border border-purple-100"><i class="fas fa-mobile-alt"></i> QRIS</span>',
                                    'card' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-orange-700 rounded-lg text-[13px] font-bold border border-orange-100"><i class="fas fa-credit-card"></i> Kartu Kredit</span>',
                                    'check' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 text-gray-700 rounded-lg text-[13px] font-bold border border-gray-100"><i class="fas fa-edit"></i> Cek</span>',
                                ];
                            @endphp
                            {!! $methods[$payment->payment_method] ?? $payment->payment_method !!}
                        </p>
                    </div>
                    @if($payment->reference_number)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">No. Referensi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $payment->reference_number }}</p>
                    </div>
                    @endif
                    @if($payment->qris_transaction_id)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">QRIS Transaction ID</p>
                        <p class="text-lg font-mono text-gray-900">{{ $payment->qris_transaction_id }}</p>
                    </div>
                    @endif
                    @if($payment->qris_status)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-600 mb-1">Status QRIS</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @php
                                $qrisStatuses = [
                                    'pending' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[13px] font-bold border border-amber-100"><i class="fas fa-hourglass-half"></i> Menunggu</span>',
                                    'success' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[13px] font-bold border border-emerald-100"><i class="fas fa-check-circle"></i> Berhasil</span>',
                                    'failed' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-[13px] font-bold border border-red-100"><i class="fas fa-times-circle"></i> Gagal</span>',
                                    'expired' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 text-gray-700 rounded-lg text-[13px] font-bold border border-gray-100"><i class="fas fa-clock"></i> Kadaluarsa</span>',
                                ];
                            @endphp
                            {!! $qrisStatuses[$payment->qris_status] ?? $payment->qris_status !!}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Student Info -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informasi Siswa
                </h2>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->student->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">NISN</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->student->nisn }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Info (if exists) -->
            @if($payment->bill)
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informasi Tagihan
                </h2>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Jenis Tagihan</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->bill->paymentType->type_name }} ({{ $payment->bill->academicYear->year }})</p>
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
                                {{ $months[$payment->bill->month] ?? $payment->bill->month }} {{ $payment->bill->year }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Tagihan</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($payment->bill->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status Tagihan</p>
                            <p class="text-lg font-semibold">
                                @if($payment->bill->status == 'lunas')
                                    <span class="text-green-600"><i class="fas fa-check-circle text-green-500 mr-1"></i> Lunas</span>
                                @elseif($payment->bill->status == 'cicilan')
                                    <span class="text-yellow-600"><i class="fas fa-hourglass-half mr-1"></i> Cicilan</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-times-circle text-red-500 mr-1"></i> Belum Bayar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Verification Info -->
            @if($payment->is_verified)
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Verifikasi
                </h2>
                <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($payment->verified_by)
                        <div>
                            <p class="text-sm text-gray-600">Diverifikasi Oleh</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->verifiedBy->name ?? 'Admin' }}
                            </p>
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
                        @if($payment->processed_by)
                        <div>
                            <p class="text-sm text-gray-600">Diproses Oleh</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $payment->processedBy->name ?? 'Admin' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($payment->notes)
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <a href="{{ route('admin.payments.index') }}" 
                class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <div class="flex gap-2">
                <a href="{{ route('admin.payments.receipt', $payment) }}" 
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Kwitansi
                </a>
                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.payments.edit', $payment) }}" 
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi pembayaran ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="flex items-center gap-2 px-4 py-2 bg-rose-600 text-white rounded-xl font-medium hover:bg-rose-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
                @endif
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
