@extends('layouts.orangtua')
@section('title', 'Tagihan '.$student->full_name.' - Portal Orang Tua')

@section('content')
<div class="space-y-6">
    @include('orangtua.partials.child-header', ['student' => $student, 'classroom' => $classroom, 'active' => 'tagihan'])

    {{-- Filter Academic Year --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('orangtua.anak.tagihan', $student->id) }}" class="flex items-center gap-3">
            <label class="text-xs font-bold text-gray-400 uppercase whitespace-nowrap"><i class="fas fa-calendar-alt mr-1"></i> Tahun Pelajaran:</label>
            <select name="academic_year_id" onchange="this.form.submit()" class="w-full sm:w-64 border border-gray-200 rounded-lg px-2.5 py-1 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                <option value="">Semua Tahun Pelajaran</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                        {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium mb-1">Total Tagihan</p>
            <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4">
            <p class="text-xs text-green-600 font-medium mb-1">Sudah Dibayar</p>
            <p class="text-xl font-bold text-green-600">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4">
            <p class="text-xs text-red-600 font-medium mb-1">Sisa Tagihan</p>
            <p class="text-xl font-bold text-red-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">📋 Daftar Tagihan</h2>
        </div>
        @if($bills->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">Jenis</th>
                            <th class="px-5 py-3 text-center font-semibold">Periode</th>
                            <th class="px-5 py-3 text-right font-semibold">Jumlah</th>
                            <th class="px-5 py-3 text-right font-semibold">Dibayar</th>
                            <th class="px-5 py-3 text-right font-semibold">Sisa</th>
                            <th class="px-5 py-3 text-center font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($bills as $bill)
                            @php
                                $sisa = $bill->amount - $bill->paid_amount;
                                $statusColor = match($bill->status) { 'lunas' => 'bg-green-100 text-green-700', 'cicilan' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' };
                                $statusLabel = match($bill->status) { 'lunas' => 'Lunas', 'cicilan' => 'Cicilan', default => 'Belum Bayar' };
                                $bulan = $bill->month ? \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') : '-';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $bill->paymentType->type_name ?? 'Tagihan' }}</td>
                                <td class="px-5 py-3 text-center text-gray-600">
                                    @if($bill->month)
                                        {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }} {{ $bill->year }}
                                    @else
                                        1 Kali ({{ $bill->academicYear->year ?? $bill->year }})
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right text-green-600">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-bold {{ $sisa > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-10 text-center text-gray-400">
                <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                <p>Tidak ada tagihan.</p>
            </div>
        @endif
    </div>
</div>
@endsection
