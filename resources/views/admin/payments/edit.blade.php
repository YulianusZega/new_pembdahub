@extends('layouts.admin')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Pembayaran</h1>
                <p class="text-gray-600 mt-1">Ubah data transaksi pembayaran siswa</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <ul class="list-disc list-inside text-red-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Informasi Siswa & No Kwitansi (Read-Only Info Header) -->
    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Siswa</label>
            <p class="font-bold text-gray-800">{{ $payment->student->full_name }}</p>
            <p class="text-xs text-gray-500">NISN: {{ $payment->student->nisn }}</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">No. Kwitansi</label>
            <p class="font-bold text-gray-800">{{ $payment->receipt_number ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Target Tagihan</label>
            <p class="font-bold text-gray-800">{{ $payment->bill ? $payment->bill->paymentType->type_name : 'Tanpa Tagihan' }}</p>
            @if($payment->bill)
            <p class="text-xs text-gray-500">Periode: {{ $payment->bill->month ? \Carbon\Carbon::create($payment->bill->year, $payment->bill->month, 1)->translatedFormat('F Y') : $payment->bill->year }}</p>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Hidden fields required by StorePaymentRequest -->
        <input type="hidden" name="student_id" value="{{ $payment->student_id }}">
        @if($payment->bill_id)
        <input type="hidden" name="bill_id" value="{{ $payment->bill_id }}">
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="space-y-5">
                <!-- Jumlah Pembayaran -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-coins mr-1"></i> Jumlah Pembayaran (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', (int)$payment->amount_paid) }}" required min="0.01" step="1000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah yang dibayarkan dalam Rupiah</p>
                </div>

                <!-- Metode & Tanggal Pembayaran -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-credit-card mr-1"></i> Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ old('payment_method', $payment->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="qris" {{ old('payment_method', $payment->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>Kartu Kredit</option>
                            <option value="check" {{ old('payment_method', $payment->payment_method) == 'check' ? 'selected' : '' }}>Cek</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Bayar <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="payment_date" value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- No Referensi & Catatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file-alt mr-1"></i> No. Referensi</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}"
                            placeholder="REF-001"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">No. referensi bank, QRIS, atau nomor bukti transaksi</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-edit mr-1"></i> Catatan</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"
                            placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Perbarui Pembayaran
            </button>
            <a href="{{ route('admin.payments.show', $payment) }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
