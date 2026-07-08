@extends('layouts.admin')

@section('title', 'Detail Tagihan')

@section('content')
<div class="space-y-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.bills.index') }}" class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-all shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Tagihan</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap tagihan dan riwayat pembayaran</p>
            </div>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.bills.edit', $bill) }}" 
               class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm shadow transition-all">
                <i class="fas fa-edit text-xs"></i> Edit Tagihan
            </a>
            @if($bill->paid_amount <= 0 && !$bill->payments()->exists())
            <form action="{{ route('admin.bills.destroy', $bill) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm shadow transition-all">
                    <i class="fas fa-trash text-xs"></i> Hapus Tagihan
                </button>
            </form>
            @endif
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Info Tagihan -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Aggregate Summary Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Ringkasan Tagihan {{ $bill->paymentType->type_name }} ({{ $bill->academicYear->year }})
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Total Tagihan (TA)</label>
                            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-500 mt-1">Tahun Ajaran: {{ $bill->academicYear->year }}</p>
                        </div>
                        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest block mb-1">Total Terbayar</label>
                            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                            <div class="mt-2 h-1.5 w-full bg-emerald-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ $totalAmount > 0 ? ($totalPaid / $totalAmount) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                            <label class="text-[10px] font-bold text-red-600 uppercase tracking-widest block mb-1">Total Tunggakan</label>
                            <p class="text-xl font-bold text-red-700">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-red-400 mt-1 font-medium italic">Sisa yang harus dilunasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Payment Schedule Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal Pembayaran Bulanan
                    </h3>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">{{ $relatedBills->count() }} Kode Tagihan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Bulan / Periode</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-right">Tagihan</th>
                                <th class="px-6 py-4 text-right">Terbayar</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $monthNames = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($relatedBills as $item)
                            <tr class="hover:bg-blue-50/30 transition-colors {{ $item->id == $bill->id ? 'bg-blue-50/50' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $monthNames[$item->month] ?? $item->month }} {{ $item->year }}</p>
                                    <p class="text-xs text-gray-400">Tempo: {{ $item->due_date ? $item->due_date->format('d/m/Y') : '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $sColors = [
                                            'belum_bayar' => 'bg-red-100 text-red-700',
                                            'cicilan' => 'bg-amber-100 text-amber-700',
                                            'lunas' => 'bg-emerald-100 text-emerald-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase {{ $sColors[$item->status] ?? 'bg-gray-100' }}">
                                        {{ $item->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-600">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                    Rp {{ number_format($item->paid_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        @if($item->status != 'lunas')
                                        <a href="{{ route('admin.payments.create', ['student_id' => $item->student_id, 'bill_id' => $item->id]) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase transition-colors">
                                            Bayar
                                        </a>
                                        @else
                                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        @endif
                                        
                                        @if(auth()->user()->isSuperAdmin())
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('admin.bills.edit', $item->id) }}" class="text-amber-600 hover:text-amber-800 font-bold text-xs uppercase transition-colors">
                                            Edit
                                        </a>
                                        @if($item->paid_amount <= 0 && !$item->payments()->exists())
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('admin.bills.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs uppercase transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- History Pembayaran Kolektif -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Riwayat Pembayaran (Semua Periode)
                    </h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">{{ $allPayments->count() }} Transaksi TA Ini</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left">Tgl Bayar</th>
                                <th class="px-6 py-3 text-left">Metode</th>
                                <th class="px-6 py-3 text-left">Periode Tagihan</th>
                                <th class="px-6 py-3 text-right">Jumlah</th>
                                @if(auth()->user()->isSuperAdmin())
                                <th class="px-6 py-3 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($allPayments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 text-sm">{{ $payment->payment_date->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $payment->payment_date->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-semibold text-gray-700">
                                        {{ $monthNames[$payment->bill->month] ?? '-' }} {{ $payment->bill->year }}
                                    </p>
                                    <p class="text-xs text-gray-400">Oleh: {{ $payment->processedBy->name ?? 'System' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</p>
                                </td>
                                @if(auth()->user()->isSuperAdmin())
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="text-amber-600 hover:text-amber-800 font-bold text-xs uppercase transition-colors">
                                            Edit
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi pembayaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs uppercase transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isSuperAdmin() ? 5 : 4 }}" class="px-6 py-10 text-center text-gray-400 italic">Belum ada catatan pembayaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Info Siswa & Status -->
        <div class="space-y-6">
            <!-- Aggregate Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-4">Status Kolektif ({{ $bill->academicYear->year }})</label>
                
                @if($totalOutstanding <= 0)
                    <div class="inline-flex items-center px-6 py-2 rounded-xl font-bold text-lg uppercase border-2 bg-emerald-100 text-emerald-700 border-emerald-200">
                        LUNAS SEMUA
                    </div>
                    <div class="mt-4 flex flex-col items-center">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-xs text-emerald-600 font-bold mt-2 uppercase tracking-tighter text-center">Seluruh tagihan periode ini telah terbayar penuh</p>
                    </div>
                @else
                    <div class="inline-flex items-center px-6 py-2 rounded-xl font-bold text-lg uppercase border-2 bg-red-100 text-red-700 border-red-200">
                        BELUM LUNAS
                    </div>
                    <div class="mt-6 flex flex-col gap-3">
                        <a href="{{ route('admin.payments.create', ['student_id' => $bill->student_id]) }}" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:scale-[1.02] active:scale-95 transition-all uppercase text-sm">
                            Bayar Tunggakan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Student Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto w-24 h-24 mb-4 rounded-2xl overflow-hidden shadow-lg border-4 border-white">
                        <img src="{{ $bill->student->photo_url }}" class="w-full h-full object-cover" alt="{{ $bill->student->full_name }}">
                    </div>
                    <h4 class="text-xl font-bold text-gray-900">{{ $bill->student->full_name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">NISN: {{ $bill->student->nisn }}</p>
                    
                    <hr class="my-6 border-gray-50">
                    
                    <div class="text-left space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">Unit Sekolah</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $bill->student->school->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">Kelas Saat Ini</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $bill->student->classrooms->first()->class_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
