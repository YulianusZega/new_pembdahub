@extends('layouts.treasurer')
@section('title', 'Slip Gaji - ' . $slipData['employee_name'])
@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .print-container { box-shadow: none !important; border: none !important; }
    }
</style>
@endpush
@section('content')
<div class="space-y-6">
    <div class="mb-4 no-print flex items-center justify-between">
        <a href="{{ route('treasurer.salary-detail', ['employee' => $employee->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <div class="flex gap-2">
            <a href="{{ route('treasurer.salary-slip-pdf', ['employee' => $employee->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8 print-container max-w-3xl mx-auto border border-gray-100">
        <!-- Header -->
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
            <h1 class="text-xl font-bold text-gray-900">{{ $slipData['school_name'] ?? 'YAYASAN PENDIDIKAN' }}</h1>
            <h2 class="text-lg font-semibold text-gray-700">SLIP GAJI PEGAWAI</h2>
            <p class="text-sm text-gray-500">Periode: {{ $slipData['period'] }}</p>
        </div>

        <!-- Employee Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <div class="flex"><span class="w-32 text-gray-600 font-semibold">Nama</span><span class="text-gray-400">:</span><span class="ml-2 font-medium text-gray-800">{{ $slipData['employee_name'] }}</span></div>
                <div class="flex"><span class="w-32 text-gray-600 font-semibold">NIP</span><span class="text-gray-400">:</span><span class="ml-2 text-gray-800">{{ $slipData['nip'] ?? '-' }}</span></div>
            </div>
            <div>
                <div class="flex"><span class="w-32 text-gray-600 font-semibold">Status</span><span class="text-gray-400">:</span><span class="ml-2 text-gray-800">{{ $slipData['employment_status'] }}</span></div>
                <div class="flex"><span class="w-32 text-gray-600 font-semibold">Jam Mengajar</span><span class="text-gray-400">:</span><span class="ml-2 text-gray-800">{{ $slipData['teaching_hours'] }} jam/minggu</span></div>
            </div>
        </div>

        <!-- Salary Components -->
        <table class="w-full text-sm mb-6">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left font-semibold text-gray-700">Komponen Gaji</th>
                    <th class="py-2 px-4 text-right font-semibold text-gray-700">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($slipData['components'] as $component)
                <tr>
                    <td class="py-2 px-4 text-gray-700">{{ $component['label'] }}</td>
                    <td class="py-2 px-4 text-right text-gray-800">{{ number_format($component['amount'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="bg-emerald-50 font-bold">
                    <td class="py-2 px-4 text-emerald-800">PENGHASILAN BRUTO</td>
                    <td class="py-2 px-4 text-right text-emerald-800">Rp {{ number_format($slipData['gross_pay'], 0, ',', '.') }}</td>
                </tr>
                
                @if(!empty($slipData['deductions']))
                    <tr class="bg-gray-100"><td colspan="2" class="py-1 px-4 text-[10px] uppercase font-bold text-gray-400">Potongan</td></tr>
                    @foreach($slipData['deductions'] as $deduction)
                    <tr class="text-red-600 italic">
                        <td class="py-2 px-4">{{ $deduction['label'] }}</td>
                        <td class="py-2 px-4 text-right">-{{ number_format($deduction['amount'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-red-50 font-bold text-red-800">
                        <td class="py-2 px-4">TOTAL POTONGAN</td>
                        <td class="py-2 px-4 text-right">Rp ({{ number_format($slipData['total_deductions'], 0, ',', '.') }})</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-800 bg-indigo-50 font-bold">
                    <td class="py-3 px-4 text-gray-900 uppercase">TAKE HOME PAY (NETTO)</td>
                    <td class="py-3 px-4 text-right text-indigo-700 text-lg">Rp {{ number_format($slipData['take_home_pay'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Terbilang -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="text-sm text-gray-600"><strong>Terbilang:</strong> <em class="text-gray-800">{{ $slipData['terbilang'] ?? '-' }}</em></p>
        </div>

        <!-- Signature -->
        <div class="grid grid-cols-2 gap-8 mt-12 text-sm text-center">
            <div>
                <p class="text-gray-600">Diterima oleh,</p>
                <div class="h-20"></div>
                <p class="font-medium border-t border-gray-400 pt-1 text-gray-800">{{ $slipData['employee_name'] }}</p>
            </div>
            <div>
                <p class="text-gray-600">Mengetahui,</p>
                <div class="h-20"></div>
                <p class="font-medium border-t border-gray-400 pt-1 text-gray-800">Kepala Sekolah / Bendahara</p>
            </div>
        </div>
    </div>
</div>
@endsection
