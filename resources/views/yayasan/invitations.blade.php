@extends('layouts.yayasan')

@section('title', 'Undangan Pelatihan PembdaHUB')

@section('content')
<div class="space-y-6" x-data="invitationManager()">
    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-violet-700 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-2xl">✉️</span>
                    <span class="bg-white/20 px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider">Persiapan TP. 2026/2027</span>
                </div>
                <h1 class="text-2xl font-bold">Undangan Pelatihan PembdaHUB & Persiapan MPLS</h1>
                <p class="text-white/70 text-sm mt-1">Kelola dan kirim undangan resmi pelatihan kepada Kepala Sekolah, Guru, dan Staf Pegawai via WhatsApp secara personal.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button @click="sendSelected()" :disabled="selectedEmployees.length === 0" 
                        class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 disabled:bg-emerald-400/50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Terpilih (<span x-text="selectedEmployees.length">0</span>)</span>
                </button>
                <button @click="sendAll()" 
                        class="inline-flex items-center gap-2 bg-white text-violet-700 hover:bg-violet-50 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-mail-bulk"></i>
                    <span>Kirim Semua (<span x-text="filteredInvitations.length">0</span>)</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-white rounded-xl shadow-lg p-5 border border-violet-50">
        <form method="GET" action="{{ route('yayasan.invitations') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Unit Sekolah</label>
                <select name="school_id" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="">Semua Sekolah</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" @selected($selectedSchoolId == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Peran Pegawai</label>
                <select name="role_type" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="">Semua Peran</option>
                    <option value="kepala_sekolah" @selected($selectedRole === 'kepala_sekolah')>Kepala Sekolah</option>
                    <option value="admin" @selected($selectedRole === 'admin')>Admin Operator</option>
                    <option value="bendahara" @selected($selectedRole === 'bendahara')>Bendahara Sekolah</option>
                    <option value="guru" @selected($selectedRole === 'guru')>Guru / Wali Kelas</option>
                    <option value="pegawai" @selected($selectedRole === 'pegawai')>Staf Pegawai</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cari Nama / Jabatan</label>
                <input type="text" x-model="searchQuery" placeholder="Cari nama atau jabatan..." 
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </form>
    </div>

    {{-- Main List Card --}}
    <div class="bg-white rounded-xl shadow-lg border border-violet-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold uppercase text-xs tracking-wider">
                        <th class="py-4 px-5 text-left w-12">
                            <input type="checkbox" @change="toggleSelectAll($el.checked)" :checked="isAllSelected()"
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-500 w-4 h-4">
                        </th>
                        <th class="py-4 px-5 text-left">Pegawai</th>
                        <th class="py-4 px-5 text-left">Unit Sekolah</th>
                        <th class="py-4 px-5 text-left">Kategori Peran</th>
                        <th class="py-4 px-5 text-left">Nomor HP</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in filteredInvitations" :key="item.employee_id">
                        <tr class="hover:bg-violet-50/30 transition-colors">
                            <td class="py-3.5 px-5">
                                <input type="checkbox" :value="item.employee_id" x-model="selectedEmployees"
                                       class="rounded border-gray-300 text-violet-600 focus:ring-violet-500 w-4 h-4">
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-medium text-gray-800" x-text="item.name"></div>
                                <div class="text-xs text-gray-400" x-text="item.position"></div>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="text-gray-600 text-xs font-semibold" x-text="item.school_name"></div>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-rose-100 text-rose-700': item.role_type === 'kepala_sekolah',
                                          'bg-blue-100 text-blue-700': item.role_type === 'admin',
                                          'bg-amber-100 text-amber-700': item.role_type === 'bendahara',
                                          'bg-violet-100 text-violet-700': item.role_type === 'guru',
                                          'bg-gray-100 text-gray-700': item.role_type === 'pegawai'
                                      }"
                                      x-text="item.role_label"></span>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-mono text-xs text-gray-600" x-text="item.phone || '-'"></div>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      :class="{
                                          'bg-gray-100 text-gray-500': item.status === 'belum_dikirim',
                                          'bg-amber-100 text-amber-700': item.status === 'pending',
                                          'bg-emerald-100 text-emerald-700': item.status === 'sent',
                                          'bg-rose-100 text-rose-700': item.status === 'failed'
                                      }">
                                    <i class="fas" :class="{
                                        'fa-clock mr-1': item.status === 'belum_dikirim',
                                        'fa-spinner fa-spin mr-1': item.status === 'pending',
                                        'fa-check-circle mr-1': item.status === 'sent',
                                        'fa-exclamation-circle mr-1': item.status === 'failed'
                                    }"></i>
                                    <span x-text="getStatusLabel(item.status)"></span>
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1">
                                <button @click="previewMessage(item)" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors"
                                        title="Preview Surat">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <button @click="sendIndividual(item)" :disabled="!item.phone"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                        title="Kirim Undangan WA">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredInvitations.length === 0">
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                            Tidak ada data pegawai yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl border text-white font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-600 border-emerald-500' : 'bg-rose-600 border-rose-500'"
         style="display: none;">
        <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
        <span x-text="toast.message"></span>
    </div>

    {{-- Modal Preview --}}
    <div x-show="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" 
         style="display: none;" @keydown.escape.window="modal.show = false">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-5 text-white flex justify-between items-center">
                <h3 class="font-bold flex items-center gap-2">
                    <i class="fas fa-file-alt"></i>
                    Preview Pesan Undangan
                </h3>
                <button @click="modal.show = false" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penerima</p>
                    <p class="text-sm font-semibold text-gray-800" x-text="modal.name + ' (' + modal.position + ')'"></p>
                    <p class="text-xs text-gray-500" x-text="'Sekolah: ' + modal.school"></p>
                    <p class="text-xs font-mono text-gray-500" x-text="'No HP: ' + (modal.phone || 'TIDAK ADA NOMOR HP')"></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Teks Pesan (Format WhatsApp)</p>
                    <pre class="bg-white border border-gray-200 rounded-xl p-4 text-xs font-sans text-gray-700 whitespace-pre-wrap leading-relaxed shadow-inner" 
                         x-text="modal.message"></pre>
                </div>
            </div>
            <div class="bg-gray-150 px-6 py-4 flex justify-end gap-2 border-t border-gray-100">
                <button @click="modal.show = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-xl text-sm">
                    Tutup
                </button>
                <button @click="sendFromModal()" :disabled="!modal.phone"
                        class="bg-emerald-500 hover:bg-emerald-600 disabled:bg-emerald-300 text-white font-semibold px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i>
                    Kirim Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function invitationManager() {
        return {
            invitations: @json($invitationsList),
            searchQuery: '',
            selectedEmployees: [],
            toast: {
                show: false,
                message: '',
                type: 'success'
            },
            modal: {
                show: false,
                employee_id: null,
                name: '',
                position: '',
                school: '',
                phone: '',
                message: ''
            },

            get filteredInvitations() {
                if (!this.searchQuery) return this.invitations;
                const query = this.searchQuery.toLowerCase();
                return this.invitations.filter(item => {
                    return item.name.toLowerCase().includes(query) || 
                           item.position.toLowerCase().includes(query);
                });
            },

            getStatusLabel(status) {
                return status === 'belum_dikirim' ? 'Belum Dikirim' :
                       status === 'pending' ? 'Diproses/Pending' :
                       status === 'sent' ? 'Terkirim (Sukses)' : 'Gagal';
            },

            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            },

            toggleSelectAll(checked) {
                if (checked) {
                    this.selectedEmployees = this.filteredInvitations.map(item => item.employee_id);
                } else {
                    this.selectedEmployees = [];
                }
            },

            isAllSelected() {
                return this.filteredInvitations.length > 0 && 
                       this.selectedEmployees.length === this.filteredInvitations.length;
            },

            previewMessage(item) {
                this.modal.employee_id = item.employee_id;
                this.modal.name = item.name;
                this.modal.position = item.position;
                this.modal.school = item.school_name;
                this.modal.phone = item.phone;
                this.modal.message = item.message;
                this.modal.show = true;
            },

            async sendIndividual(item) {
                item.status = 'pending';
                try {
                    const response = await fetch("{{ route('yayasan.invitations.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            employee_id: item.employee_id,
                            message: item.message
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        item.status = 'sent';
                        this.showToast(data.message, 'success');
                    } else {
                        item.status = 'failed';
                        this.showToast(data.message || 'Gagal mengirim pesan.', 'error');
                    }
                } catch (error) {
                    item.status = 'failed';
                    this.showToast('Koneksi terganggu. Silakan coba lagi.', 'error');
                }
            },

            async sendFromModal() {
                this.modal.show = false;
                const item = this.invitations.find(i => i.employee_id === this.modal.employee_id);
                if (item) {
                    await this.sendIndividual(item);
                }
            },

            async sendSelected() {
                const selectedList = this.invitations.filter(item => this.selectedEmployees.includes(item.employee_id));
                const recipients = selectedList.map(item => {
                    item.status = 'pending';
                    return {
                        employee_id: item.employee_id,
                        message: item.message
                    };
                });

                try {
                    const response = await fetch("{{ route('yayasan.invitations.send_bulk') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ recipients })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        selectedList.forEach(item => item.status = 'sent');
                        this.showToast(data.message, 'success');
                        this.selectedEmployees = [];
                    } else {
                        selectedList.forEach(item => item.status = 'failed');
                        this.showToast(data.message || 'Gagal mengirim bulk.', 'error');
                    }
                } catch (error) {
                    selectedList.forEach(item => item.status = 'failed');
                    this.showToast('Gagal memproses pengiriman massal.', 'error');
                }
            },

            async sendAll() {
                if (!confirm(`Kirim undangan WhatsApp ke semua (${this.filteredInvitations.length}) pegawai yang tampil?`)) {
                    return;
                }
                const recipients = this.filteredInvitations.map(item => {
                    item.status = 'pending';
                    return {
                        employee_id: item.employee_id,
                        message: item.message
                    };
                });

                try {
                    const response = await fetch("{{ route('yayasan.invitations.send_bulk') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ recipients })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.filteredInvitations.forEach(item => item.status = 'sent');
                        this.showToast(data.message, 'success');
                        this.selectedEmployees = [];
                    } else {
                        this.filteredInvitations.forEach(item => item.status = 'failed');
                        this.showToast(data.message || 'Gagal mengirim bulk.', 'error');
                    }
                } catch (error) {
                    this.filteredInvitations.forEach(item => item.status = 'failed');
                    this.showToast('Gagal memproses pengiriman massal.', 'error');
                }
            }
        };
    }
</script>
@endsection
