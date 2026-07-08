@extends('layouts.admin')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Kelola pembayaran siswa</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.payments.bulk-create') }}" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-700 text-white rounded-xl font-medium hover:from-indigo-700 hover:to-purple-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Pembayaran Massal
                </a>
                <a href="{{ route('admin.payments.create') }}" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Catat Pembayaran
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tahun Ajaran -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Tahun Ajaran</label>
                    <div class="relative group">
                        <select name="academic_year_id" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Sekolah -->
                @if(auth()->user()->isSuperAdmin())
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Sekolah</label>
                    <div class="relative group">
                        <select name="school_id" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Sekolah</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Kelas -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Kelas</label>
                    <div class="relative group">
                        <select name="classroom_id" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Jenis Pembayaran -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Jenis Pembayaran</label>
                    <div class="relative group">
                        <select name="payment_type_id" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Jenis</option>
                            @foreach($paymentTypes as $type)
                                <option value="{{ $type->id }}" {{ $paymentTypeId == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Status</label>
                    <div class="relative group">
                        <select name="is_verified" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="1" {{ $isVerified == '1' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="0" {{ $isVerified == '0' ? 'selected' : '' }}>Belum Verifikasi</option>
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Metode -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Metode</label>
                    <div class="relative group">
                        <select name="payment_method" onchange="this.form.submit()" 
                            class="w-full pl-11 pr-10 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Metode</option>
                            <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ $paymentMethod == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="qris" {{ $paymentMethod == 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Cari -->
                <div class="space-y-2 {{ auth()->user()->isSuperAdmin() ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    <label class="text-sm font-bold text-gray-700 ml-1">Pencarian</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ $search }}" 
                            placeholder="Cari nama siswa atau NISN..."
                            class="w-full pl-11 pr-4 py-3 bg-white border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.payments.index', ['reset' => 1]) }}" 
                    class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all">
                    Reset Filter
                </a>
                <a href="{{ route('admin.payments.export', ['is_verified' => $isVerified, 'payment_method' => $paymentMethod, 'search' => $search, 'school_id' => $schoolId, 'classroom_id' => $classroomId, 'payment_type_id' => $paymentTypeId, 'academic_year_id' => $academicYearId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                    class="flex items-center gap-2 px-6 py-3 bg-blue-50 text-blue-600 rounded-xl font-bold hover:bg-blue-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
                <button type="submit" 
                    class="flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan & Periode</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $index => $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white text-sm font-bold">
                            {{ $payments->firstItem() + $index }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $payment->payment_date->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold">{{ $payment->student->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->student->nisn }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->bill)
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $payment->bill->paymentType->type_name }} ({{ $payment->bill->academicYear->year }})</p>
                                @if($payment->bill->month)
                                    <p class="text-xs text-gray-500">Periode: {{ \Carbon\Carbon::create(null, $payment->bill->month, 1)->translatedFormat('F') }} {{ $payment->bill->year }}</p>
                                @else
                                    <p class="text-xs text-gray-500">1 Kali Bayar ({{ $payment->bill->year }})</p>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-500 italic text-sm">Pembayaran Massal / Umum</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-blue-600">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $methods = [
                                'cash' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[11px] font-bold border border-emerald-100"><i class="fas fa-money-bill text-[10px]"></i> Tunai</span>',
                                'transfer' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[11px] font-bold border border-blue-100"><i class="fas fa-university text-[10px]"></i> Transfer</span>',
                                'qris' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-[11px] font-bold border border-purple-100"><i class="fas fa-mobile-alt text-[10px]"></i> QRIS</span>',
                                'card' => '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-orange-700 rounded-lg text-[11px] font-bold border border-orange-100"><i class="fas fa-credit-card text-[10px]"></i> Kartu</span>',
                            ];
                        @endphp
                        {!! $methods[$payment->payment_method] ?? '<span class="inline-flex items-center px-2.5 py-1 bg-gray-50 text-gray-700 rounded-lg text-[11px] font-bold border border-gray-100">'.strtoupper($payment->payment_method).'</span>' !!}
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->is_verified)
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full"><i class="fas fa-check text-green-500 mr-1"></i> Terverifikasi</span>
                        @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">⊙ Belum Verifikasi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.payments.show', $payment) }}" 
                                class="w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:scale-110 transform transition-all shadow-sm"
                                title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.payments.edit', $payment) }}" 
                                class="w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 text-white hover:scale-110 transform transition-all shadow-sm"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi pembayaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 text-white hover:scale-110 transform transition-all shadow-sm"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p class="text-lg">Tidak ada data pembayaran</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
