@extends('layouts.admin')

@section('title', 'Riwayat Pembayaran - ' . $student->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.students.show', $student) }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Pembayaran</h1>
                <p class="text-gray-600 mt-1">{{ $student->full_name }} ({{ $student->nisn }})</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-purple-100 text-sm mb-1">Total Tagihan</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalBilled, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm mb-1">Sudah Dibayar</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-red-100 text-sm mb-1">Sisa Tunggakan</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-yellow-100 text-sm mb-1">Denda Keterlambatan</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalLateFees, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Payment Timeline -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Timeline Pembayaran</h2>
        
        @forelse($payments as $payment)
        <div class="flex gap-4 pb-6 mb-6 border-b border-gray-200 last:border-0">
            <!-- Date Badge -->
            <div class="flex-shrink-0">
                <div class="bg-green-100 text-green-700 rounded-xl px-4 py-2 text-center">
                    <div class="text-xs font-semibold">{{ $payment->payment_date->format('M') }}</div>
                    <div class="text-2xl font-bold">{{ $payment->payment_date->format('d') }}</div>
                    <div class="text-xs">{{ $payment->payment_date->format('Y') }}</div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="flex-grow">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $payment->bill ? $payment->bill->paymentType->type_name : 'Pembayaran Umum' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            No. Kwitansi: <span class="font-semibold">{{ $payment->receipt_number }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                        </p>
                        @if($payment->is_verified)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <i class="fas fa-check text-green-500 mr-1"></i> Terverifikasi
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            ⊙ Belum Verifikasi
                        </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs text-gray-500">Metode Pembayaran</p>
                        <p class="text-sm font-semibold text-gray-900">
                            @switch($payment->payment_method)
                                @case('cash') Tunai @break
                                @case('transfer') Transfer @break
                                @case('qris') QRIS @break
                                @case('card') Kartu @break
                                @default {{ $payment->payment_method }}
                            @endswitch
                        </p>
                    </div>
                    @if($payment->reference_number)
                    <div>
                        <p class="text-xs text-gray-500">No. Referensi</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->reference_number }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">Diproses Oleh</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->processedBy->name ?? '-' }}</p>
                    </div>
                    @if($payment->notes)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Catatan</p>
                        <p class="text-sm text-gray-700">{{ $payment->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Download Receipt Button -->
                @if($payment->receipt_number)
                <div class="mt-3">
                    <a href="{{ route('admin.payments.receipt', $payment) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Kwitansi
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 text-lg">Belum ada riwayat pembayaran</p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

    <!-- All Bills Table -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Semua Tagihan</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Jenis Tagihan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tahun Ajaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Dibayar</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Denda</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $bill->paymentType->type_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $bill->academicYear->year }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            @if($bill->month)
                                {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$bill->month] }} {{ $bill->year }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($bill->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-green-600">
                            Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-orange-600">
                            @if($bill->late_fee > 0)
                                Rp {{ number_format($bill->late_fee, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold {{ $bill->amount - $bill->paid_amount > 0 ? 'text-red-600' : 'text-gray-400' }}">
                            Rp {{ number_format($bill->amount - $bill->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($bill->status === 'lunas')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Lunas
                                </span>
                            @elseif($bill->status === 'cicilan')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Cicilan
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    Belum Bayar
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
