@extends('layouts.admin')

@section('title', 'Pembayaran Massal')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pembayaran Massal</h1>
                <p class="text-gray-600 mt-1">Input pembayaran untuk banyak siswa sekaligus per kelas</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
        <div class="flex">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <div>
                <h3 class="text-red-800 font-medium">Terdapat kesalahan:</h3>
                <ul class="list-disc list-inside text-red-700 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form id="bulkPaymentForm" method="POST" action="{{ route('admin.payments.bulk-store') }}">
        @csrf

        <!-- Step 1: Filter -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white text-sm font-bold">1</span>
                Filter Tagihan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Tahun Ajaran</label>
                    <select name="academic_year_id" id="academic_year_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                    <select name="classroom_id" id="classroom_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->class_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-clipboard mr-1"></i> Jenis Tagihan</label>
                    <select name="payment_type_id" id="payment_type_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($paymentTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Bulan (Untuk SPP)</label>
                    <select name="month" id="month" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Bulan --</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                    </select>
                </div>
            </div>

            <button type="button" onclick="loadBills()" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all font-medium">
                <i class="fas fa-search mr-1"></i> Tampilkan Tagihan
            </button>
        </div>

        <!-- Step 2: Select Bills -->
        <div id="billsSection" class="bg-white rounded-2xl shadow-lg p-6 mb-6" style="display: none;">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white text-sm font-bold">2</span>
                Pilih Tagihan yang Akan Dibayar
            </h2>

            <div class="mb-4 flex items-center gap-3">
                <button type="button" onclick="selectAll()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    <i class="fas fa-check text-green-500 mr-1"></i> Pilih Semua
                </button>
                <button type="button" onclick="deselectAll()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                    <i class="fas fa-times text-red-500 mr-1"></i> Batal Pilih
                </button>
                <span id="selectedCount" class="text-sm font-semibold text-gray-700">0 tagihan dipilih</span>
            </div>

            <div id="billsTableContainer" class="overflow-x-auto">
                <!-- Bills will be loaded here -->
            </div>
        </div>

        <!-- Step 3: Payment Details -->
        <div id="paymentSection" class="bg-white rounded-2xl shadow-lg p-6 mb-6" style="display: none;">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white text-sm font-bold">3</span>
                Detail Pembayaran
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-credit-card mr-1"></i> Metode Pembayaran</label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Metode --</option>
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="card">Kartu Debit/Kredit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Pembayaran</label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-edit mr-1"></i> Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan pembayaran..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Summary & Submit -->
        <div id="summarySection" class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white" style="display: none;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold">Total Pembayaran</h3>
                    <p class="text-blue-100 text-sm" id="summaryDetails">0 tagihan dipilih</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold" id="totalAmount">Rp 0</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.payments.index') }}" class="px-6 py-3 bg-white text-blue-600 rounded-xl hover:bg-blue-50 transition-all font-medium">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-white text-blue-600 rounded-xl hover:bg-blue-50 transition-all font-medium shadow-lg">
                    <i class="fas fa-coins mr-1"></i> Proses <span id="submitCount">0</span> Pembayaran
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let selectedBills = [];

function loadBills() {
    const academicYearId = document.getElementById('academic_year_id').value;
    const classroomId = document.getElementById('classroom_id').value;
    const paymentTypeId = document.getElementById('payment_type_id').value;
    const month = document.getElementById('month').value;

    if (!academicYearId || !classroomId || !paymentTypeId) {
        alert('Harap pilih Tahun Ajaran, Kelas, dan Jenis Tagihan!');
        return;
    }

    // Show loading
    const billsSection = document.getElementById('billsSection');
    billsSection.style.display = 'block';
    document.getElementById('billsTableContainer').innerHTML = '<p class="text-center py-4">Memuat data...</p>';

    // Fetch bills via AJAX
    fetch(`{{ route('admin.payments.fetch-bills') }}?academic_year_id=${academicYearId}&classroom_id=${classroomId}&payment_type_id=${paymentTypeId}&month=${month}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server tidak mengembalikan JSON. Mungkin session habis atau error server.');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                showFlashMessage(data.message || 'Terjadi kesalahan saat memuat data.', 'error');
                document.getElementById('billsTableContainer').innerHTML = '<p class="text-center py-4 text-red-500">Gagal memuat data.</p>';
                return;
            }

            if (data.bills.length === 0) {
                document.getElementById('billsTableContainer').innerHTML = '<p class="text-center py-4 text-gray-500">Tidak ada tagihan yang belum lunas ditemukan.</p>';
                return;
            }

            let html = `
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                            </th>
                            <th class="px-4 py-3 text-left">Siswa</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-center">Bulan</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-right">Biaya Admin</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
            `;

            data.bills.forEach(bill => {
                const lateFee = parseFloat(bill.late_fee || 0);
                const amount = parseFloat(bill.amount);
                const total = amount + lateFee;
                const monthName = bill.month ? getMonthName(bill.month) : '-';

                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="bill-checkbox" value="${bill.id}" 
                                   data-amount="${total}" 
                                   onchange="updateSelection()">
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">${bill.student.full_name}</p>
                            <p class="text-xs text-gray-500">${bill.student.nisn}</p>
                        </td>
                        <td class="px-4 py-3">${bill.payment_type.type_name}</td>
                        <td class="px-4 py-3 text-center">${monthName}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp ${new Intl.NumberFormat('id-ID').format(amount)}</td>
                        <td class="px-4 py-3 text-right ${lateFee > 0 ? 'text-orange-600' : 'text-gray-400'}">
                            ${lateFee > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(lateFee) : '-'}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(total)}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                ${bill.status === 'cicilan' ? 'Cicilan' : 'Belum Bayar'}
                            </span>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            document.getElementById('billsTableContainer').innerHTML = html;

            // Show payment section
            document.getElementById('paymentSection').style.display = 'block';
        })
        .catch(error => {
            showFlashMessage('Terjadi kesalahan saat memuat tagihan. Silakan coba lagi.', 'error');
            document.getElementById('billsTableContainer').innerHTML = '<p class="text-center py-4 text-red-500">Gagal memuat data.</p>';
        });
}

function getMonthName(month) {
    const months = {
        1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr', 5: 'Mei', 6: 'Jun',
        7: 'Jul', 8: 'Agu', 9: 'Sep', 10: 'Okt', 11: 'Nov', 12: 'Des'
    };
    return months[month] || '-';
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.bill-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelection();
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.bill-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelection();
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.bill-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.bill-checkbox:checked');
    const count = checkboxes.length;
    let total = 0;

    selectedBills = [];
    checkboxes.forEach(cb => {
        selectedBills.push(cb.value);
        total += parseFloat(cb.dataset.amount);
    });

    document.getElementById('selectedCount').textContent = `${count} tagihan dipilih`;
    document.getElementById('summaryDetails').textContent = `${count} tagihan dipilih`;
    document.getElementById('totalAmount').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
    document.getElementById('submitCount').textContent = count;

    // Show/hide summary
    const summarySection = document.getElementById('summarySection');
    if (count > 0) {
        summarySection.style.display = 'block';
    } else {
        summarySection.style.display = 'none';
    }
}

// Form submission
document.getElementById('bulkPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (selectedBills.length === 0) {
        alert('Harap pilih minimal 1 tagihan!');
        return;
    }

    // Add selected bills to form
    const form = this;
    selectedBills.forEach(billId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bill_ids[]';
        input.value = billId;
        form.appendChild(input);
    });

    // Show confirmation
    if (confirm(`Anda akan memproses ${selectedBills.length} pembayaran. Lanjutkan?`)) {
        form.submit();
    }
});
</script>
@endsection
