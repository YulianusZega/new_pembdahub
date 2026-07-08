@extends('layouts.admin')

@section('title', 'Pengaturan Biaya Administrasi Keterlambatan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pengaturan Biaya Administrasi Keterlambatan</h1>
                    <p class="text-gray-600 mt-1">Konfigurasi biaya administrasi untuk tagihan yang terlambat (Default: Rp 0)</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <ul class="list-disc list-inside text-red-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.settings.late-fees.update') }}" method="POST" id="settingsForm">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
                    <!-- Enable/Disable Toggle -->
                    <div class="border-b pb-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Status Sistem Biaya Administrasi
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $settings['enabled'] ? 'Sistem biaya administrasi aktif dan akan menghitung biaya secara otomatis' : 'Sistem biaya administrasi dinonaktifkan, tidak ada biaya tambahan' }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" name="late_fee_enabled" value="1" class="sr-only peer" 
                                    {{ $settings['enabled'] ? 'checked' : '' }} id="enableToggle">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Grace Period -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-1"></i> Masa Tenggang (Grace Period)
                        </label>
                        <div class="relative">
                            <input type="number" name="late_fee_grace_period" id="gracePeriod"
                                value="{{ old('late_fee_grace_period', $settings['grace_period']) }}"
                                min="0" max="30" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-16">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">hari</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Jumlah hari setelah jatuh tempo sebelum biaya administrasi mulai berlaku. 
                            Misalnya: 3 hari = biaya baru dikenakan pada hari ke-4.
                        </p>
                    </div>

                    <!-- Fee Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-coins mr-1"></i> Jumlah Biaya Administrasi
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number" name="late_fee_amount" id="feeAmount"
                                value="{{ old('late_fee_amount', $settings['amount']) }}"
                                min="0" step="1000" required
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-2" id="amountHelp">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Untuk tipe <strong>Fixed</strong>: jumlah tetap per tagihan. 
                            Untuk tipe <strong>Percentage</strong>: persentase dari sisa tunggakan.
                        </p>
                    </div>

                    <!-- Fee Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chart-bar mr-1"></i> Tipe Biaya Administrasi
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-orange-300 {{ $settings['type'] === 'fixed' ? 'border-orange-600 bg-orange-50' : 'border-gray-200' }}">
                                <input type="radio" name="late_fee_type" value="fixed" 
                                    {{ $settings['type'] === 'fixed' ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-600 focus:ring-orange-500" id="typeFixed">
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">Fixed Amount</p>
                                    <p class="text-xs text-gray-600">Jumlah tetap per tagihan</p>
                                </div>
                            </label>
                            <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-orange-300 {{ $settings['type'] === 'percentage' ? 'border-orange-600 bg-orange-50' : 'border-gray-200' }}">
                                <input type="radio" name="late_fee_type" value="percentage" 
                                    {{ $settings['type'] === 'percentage' ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-600 focus:ring-orange-500" id="typePercentage">
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">Percentage</p>
                                    <p class="text-xs text-gray-600">Persentase dari tunggakan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit" 
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-red-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Pengaturan
                        </button>
                        <a href="{{ route('admin.dashboard') }}" 
                            class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Real-time Preview -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-lg p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Perhitungan Biaya
                </h3>

                <!-- Example Scenario -->
                <div class="space-y-4 mb-6">
                    <div class="bg-white rounded-xl p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Contoh Skenario:</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tagihan SPP:</span>
                                <span class="font-semibold">Rp 500,000</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sudah Dibayar:</span>
                                <span class="font-semibold">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sisa Tunggakan:</span>
                                <span class="font-semibold text-red-600">Rp 500,000</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-600">Hari Terlambat:</span>
                                <span class="font-semibold" id="previewDays">5 hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-100 border-2 border-orange-300 rounded-xl p-4">
                        <p class="text-sm font-semibold text-orange-900 mb-3">Perhitungan Biaya Administrasi:</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-orange-800">Grace Period:</span>
                                <span class="font-semibold" id="previewGrace">3 hari</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-800">Hari Kena Biaya:</span>
                                <span class="font-semibold" id="previewChargeable">2 hari</span>
                            </div>
                            <div class="flex justify-between border-t border-orange-300 pt-2">
                                <span class="text-orange-900 font-semibold">Biaya Administrasi:</span>
                                <span class="font-bold text-orange-900" id="previewFee">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-100 border-2 border-red-300 rounded-xl p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-red-900">Total yang Harus Dibayar:</span>
                            <span class="text-xl font-bold text-red-900" id="previewTotal">Rp 510,000</span>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-xs text-blue-800 leading-relaxed">
                        <strong><i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Catatan:</strong> Preview ini menggunakan contoh tagihan SPP Rp 500,000 yang terlambat 5 hari. 
                        Biaya administrasi akan dihitung otomatis untuk semua tagihan yang melewati masa tenggang (Default: Rp 0).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time preview update
const gracePeriodInput = document.getElementById('gracePeriod');
const feeAmountInput = document.getElementById('feeAmount');
const typeFixedInput = document.getElementById('typeFixed');
const typePercentageInput = document.getElementById('typePercentage');
const enableToggle = document.getElementById('enableToggle');

// Preview elements
const previewGrace = document.getElementById('previewGrace');
const previewChargeable = document.getElementById('previewChargeable');
const previewFee = document.getElementById('previewFee');
const previewTotal = document.getElementById('previewTotal');
const amountHelp = document.getElementById('amountHelp');

// Example values
const billAmount = 500000;
const paidAmount = 0;
const daysOverdue = 5;

function updatePreview() {
    const gracePeriod = parseInt(gracePeriodInput.value) || 0;
    const feeAmount = parseInt(feeAmountInput.value) || 0;
    const feeType = typePercentageInput.checked ? 'percentage' : 'fixed';
    const enabled = enableToggle.checked;

    // Update grace period display
    previewGrace.textContent = `${gracePeriod} hari`;

    // Calculate chargeable days
    const chargeableDays = Math.max(0, daysOverdue - gracePeriod);
    previewChargeable.textContent = `${chargeableDays} hari`;

    // Calculate late fee
    let lateFee = 0;
    if (enabled && chargeableDays > 0) {
        const outstanding = billAmount - paidAmount;
        if (feeType === 'percentage') {
            lateFee = (outstanding * feeAmount) / 100;
        } else {
            lateFee = feeAmount;
        }
    }

    // Update displays
    previewFee.textContent = enabled ? `Rp ${lateFee.toLocaleString('id-ID')}` : 'Rp 0 (Sistem Nonaktif)';
    const total = billAmount + lateFee;
    previewTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;

    // Update help text
    if (feeType === 'percentage') {
        amountHelp.innerHTML = '<i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Tipe <strong>Percentage</strong> aktif: masukkan persentase (contoh: 2 untuk 2% dari tunggakan).';
    } else {
        amountHelp.innerHTML = '<i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Tipe <strong>Fixed</strong> aktif: masukkan jumlah tetap dalam Rupiah (contoh: 10000 untuk Rp 10,000).';
    }
}

// Event listeners
gracePeriodInput.addEventListener('input', updatePreview);
feeAmountInput.addEventListener('input', updatePreview);
typeFixedInput.addEventListener('change', updatePreview);
typePercentageInput.addEventListener('change', updatePreview);
enableToggle.addEventListener('change', updatePreview);

// Initial update
updatePreview();

// Form confirmation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const enabled = enableToggle.checked;
    const message = enabled 
        ? 'Apakah Anda yakin ingin menyimpan pengaturan ini? Sistem biaya administrasi akan aktif dan menghitung biaya secara otomatis.'
        : 'Apakah Anda yakin ingin menonaktifkan sistem biaya administrasi? Tidak ada biaya tambahan yang akan dihitung.';
    
    if (!confirm(message)) {
        e.preventDefault();
    }
});
</script>
@endsection
