@extends('layouts.treasurer')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Daftar Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Riwayat pembayaran siswa</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('treasurer.payments.bulk-create') }}" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Pembayaran Massal
                </a>
                <a href="{{ route('treasurer.payments.create') }}" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Pembayaran
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('treasurer.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Kartu</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <select name="classroom_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pembayaran</label>
                <select name="payment_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Jenis</option>
                    @foreach($paymentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('payment_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="No Kwitansi, Siswa..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 md:col-span-3 lg:col-span-6 justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all font-semibold shadow-md">
                    Filter
                </button>
                <a href="{{ route('treasurer.payments.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold">
                    Reset
                </a>
                <a href="{{ route('treasurer.payments.export', ['payment_method' => request('payment_method'), 'search' => request('search'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'classroom_id' => request('classroom_id'), 'payment_type_id' => request('payment_type_id')]) }}" 
                    class="flex items-center gap-2 px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Kwitansi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan & Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $payment->receipt_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $payment->student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->student->nisn }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($payment->bill)
                                <span class="font-semibold text-gray-900">{{ $payment->bill->paymentType->type_name }} ({{ $payment->bill->academicYear->year }})</span>
                                @if($payment->bill->month)
                                    <span class="text-xs text-gray-500 block">Periode: {{ \Carbon\Carbon::create(null, $payment->bill->month, 1)->translatedFormat('F') }} {{ $payment->bill->year }}</span>
                                @else
                                    <span class="text-xs text-gray-500 block">1 Kali Bayar ({{ $payment->bill->year }})</span>
                                @endif
                            @else
                                <span class="text-gray-500 italic">Pembayaran Massal / Umum</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-800' : '' }} {{ $payment->payment_method === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }} {{ $payment->payment_method === 'qris' ? 'bg-purple-100 text-purple-800' : '' }} {{ $payment->payment_method === 'card' ? 'bg-orange-100 text-orange-800' : '' }}">
                                {{ strtoupper($payment->payment_method) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($payment->is_verified)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Terverifikasi</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('treasurer.payments.show', $payment) }}" 
                                class="text-blue-600 hover:text-blue-800" title="Detail">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada pembayaran ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
