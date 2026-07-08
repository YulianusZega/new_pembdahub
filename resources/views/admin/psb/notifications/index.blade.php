@extends('layouts.admin')

@section('title', 'Notifikasi PSB')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-bell"></i> Notifikasi PSB
                </h1>
                <p class="text-white/70 text-sm mt-1">Kelola notifikasi WhatsApp untuk calon siswa</p>
            </div>
            <a href="{{ route('admin.psb.applicants.index') }}" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke PSB
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg">
            <p class="font-semibold"><i class="fas fa-check-circle mr-1"></i> {{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}</p>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Total Pendaftar</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white shadow">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['with_phone'] }}</p>
                    <p class="text-xs text-gray-500">Punya WhatsApp</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['with_email'] }}</p>
                    <p class="text-xs text-gray-500">Punya Email</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $stats['wa_enabled'] ? 'from-emerald-500 to-emerald-600' : 'from-red-500 to-red-600' }} flex items-center justify-center text-white shadow">
                    <i class="fas fa-{{ $stats['wa_enabled'] ? 'check' : 'times' }}-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['wa_enabled'] ? 'Aktif' : 'Nonaktif' }}</p>
                    <p class="text-xs text-gray-500">Status WhatsApp</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-teal-600"></i> Aksi Cepat
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <button type="button" id="openBulkModal" class="px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-medium hover:shadow-lg transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> Kirim Notifikasi Massal
            </button>
            <button type="button" onclick="testWhatsAppConnection()" class="px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl font-medium hover:shadow-lg transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-vial"></i> Test Koneksi WhatsApp
            </button>
        </div>
    </div>

    {{-- Applicants Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-list text-teal-600"></i> Daftar Pendaftar
            </h3>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                Pilih Semua
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-10">
                            <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Reg</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sekolah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($applicants as $applicant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="applicant-check rounded border-gray-300 text-teal-600 focus:ring-teal-500" value="{{ $applicant->id }}">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-xs font-bold text-gray-900">{{ $applicant->registration_number }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm text-gray-800">{{ $applicant->full_name }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-xs text-gray-600">{{ $applicant->school->name }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($applicant->phone)
                                <span class="text-xs text-green-600"><i class="fab fa-whatsapp mr-1"></i>{{ $applicant->phone }}</span>
                            @else
                                <span class="text-xs text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-{{ $applicant->getStatusBadgeColor() }}-100 text-{{ $applicant->getStatusBadgeColor() }}-800">
                                {{ $applicant->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                <button onclick="previewNotification('{{ $applicant->id }}')" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Preview">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </button>
                                <button onclick="sendPaymentConfirmation({{ $applicant->id }})" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition" title="Konfirmasi Bayar">
                                    <i class="fas fa-money-bill text-[10px]"></i>
                                </button>
                                <button onclick="sendTestSchedule({{ $applicant->id }})" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition" title="Kirim Jadwal Tes">
                                    <i class="fas fa-calendar text-[10px]"></i>
                                </button>
                                <button onclick="sendCustomMessage({{ $applicant->id }})" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition" title="Pesan Custom">
                                    <i class="fas fa-comment text-[10px]"></i>
                                </button>
                                <button onclick="resendRegistration({{ $applicant->id }})" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition" title="Kirim Ulang">
                                    <i class="fas fa-redo text-[10px]"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="text-sm">Belum ada pendaftar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applicants->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $applicants->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Bulk Notification Modal --}}
<div id="bulkNotificationModal" class="hidden fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-0 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <form method="POST" action="{{ route('admin.psb.notifications.bulk') }}">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-paper-plane text-teal-600"></i> Kirim Notifikasi Massal
                    </h3>
                    <button type="button" id="closeBulkModal" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition text-gray-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" name="applicant_ids" id="bulkApplicantIds">
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Notifikasi</label>
                        <select name="notification_type" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500" id="bulkNotificationType" required>
                            <option value="">Pilih tipe...</option>
                            <option value="registration">Konfirmasi Pendaftaran</option>
                            <option value="payment">Konfirmasi Pembayaran</option>
                            <option value="test_schedule">Jadwal Tes</option>
                            <option value="acceptance">Penerimaan</option>
                            <option value="reminder">Pengingat Custom</option>
                        </select>
                    </div>

                    <div id="paymentFields" style="display:none;">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah Pembayaran</label>
                        <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    <div id="testScheduleFields" style="display:none;" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Tes</label>
                            <input type="date" name="test_date" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Waktu</label>
                            <input type="text" name="test_time" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="08:00 - 12:00 WIB">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                            <input type="text" name="test_location" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                    </div>

                    <div id="reminderFields" style="display:none;">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pesan Pengingat</label>
                        <textarea name="reminder_message" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                    </div>

                    <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3">
                        <p class="text-xs text-teal-700"><strong>Dipilih:</strong> <span id="selectedCount">0</span> pendaftar</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3">
                    <button type="button" id="closeBulkModal2" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-teal-600 text-white rounded-xl text-sm font-medium hover:bg-teal-700 transition flex items-center justify-center gap-1">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Modal toggle
document.getElementById('openBulkModal').addEventListener('click', function() {
    document.getElementById('bulkNotificationModal').classList.remove('hidden');
});
['closeBulkModal', 'closeBulkModal2'].forEach(id => {
    document.getElementById(id).addEventListener('click', function() {
        document.getElementById('bulkNotificationModal').classList.add('hidden');
    });
});

// Select all checkboxes
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.applicant-check').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.applicant-check').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('checkAll').checked = this.checked;
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.applicant-check').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.applicant-check:checked');
    document.getElementById('selectedCount').textContent = checked.length;
    const ids = Array.from(checked).map(cb => cb.value);
    document.getElementById('bulkApplicantIds').value = JSON.stringify(ids);
}

// Show/hide fields based on notification type
document.getElementById('bulkNotificationType').addEventListener('change', function() {
    document.getElementById('paymentFields').style.display = this.value === 'payment' ? 'block' : 'none';
    document.getElementById('testScheduleFields').style.display = this.value === 'test_schedule' ? 'block' : 'none';
    document.getElementById('reminderFields').style.display = this.value === 'reminder' ? 'block' : 'none';
});

// Test WhatsApp connection
function testWhatsAppConnection() {
    fetch('{{ route('admin.psb.notifications.test') }}')
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showFlashMessage('Koneksi WhatsApp berhasil!', 'success');
            } else {
                showFlashMessage('Koneksi gagal: ' + (data.message || 'Kesalahan tidak diketahui'), 'error');
            }
        })
        .catch(() => {
            showFlashMessage('Gagal menguji koneksi WhatsApp. Silakan coba lagi.', 'error');
        });
}

// Preview notification
function previewNotification(applicantId) {
    const type = prompt('Tipe notifikasi (registration/payment/test_schedule/acceptance):');
    if (!type) return;
    fetch('{{ route('admin.psb.notifications.preview') }}?' + new URLSearchParams({
        type: type,
        applicant_id: applicantId
    }))
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert('Preview untuk: ' + data.phone + '\n\n' + data.preview);
        } else {
            showFlashMessage('Gagal memuat preview notifikasi.', 'error');
        }
    })
    .catch(() => {
        showFlashMessage('Gagal memuat preview notifikasi. Silakan coba lagi.', 'error');
    });
}

// Send functions
function sendPaymentConfirmation(id) {
    const amount = prompt('Masukkan jumlah pembayaran:');
    if (!amount) return;
    if (confirm('Kirim konfirmasi pembayaran?')) {
        submitForm(`/admin/psb/notifications/${id}/payment`, { amount: amount });
    }
}

function sendTestSchedule(id) {
    const date = prompt('Tanggal tes (YYYY-MM-DD):');
    const time = prompt('Waktu (contoh: 08:00 - 12:00 WIB):');
    const location = prompt('Lokasi:');
    if (date && time && location) {
        submitForm(`/admin/psb/notifications/${id}/test-schedule`, {
            test_date: date,
            test_time: time,
            test_location: location
        });
    }
}

function sendCustomMessage(id) {
    const message = prompt('Tulis pesan:');
    if (!message) return;
    submitForm(`/admin/psb/notifications/${id}/custom`, { message: message });
}

function resendRegistration(id) {
    if (confirm('Kirim ulang notifikasi pendaftaran?')) {
        submitForm(`/admin/psb/notifications/${id}/resend-registration`, {});
    }
}

function submitForm(url, data) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    for (let key in data) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
