<!-- Modal Update RFID -->
<div id="rfidModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" onclick="closeRfidModal()"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-lg transform transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form id="rfidForm" method="POST" action="">
                @csrf
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-id-card"></i> Daftarkan Kartu RFID
                    </h3>
                </div>
                
                <div class="px-6 py-6 space-y-4">
                    {{-- Status Indikator Fokus --}}
                    <div id="rfid_focus_status" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 transition-all">
                        <span id="rfid_focus_dot" class="inline-block w-3 h-3 rounded-full bg-red-400 flex-shrink-0"></span>
                        <p id="rfid_focus_text" class="text-sm font-medium text-red-700">Input belum aktif — klik kotak scan di bawah dulu</p>
                    </div>

                    <div>
                        <label id="rfid_label_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
                        <input type="text" id="rfid_entity_name" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-gray-700 font-semibold" disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">RFID UID / Kode Kartu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i id="rfid_icon" class="fas fa-wifi text-gray-400"></i>
                            </div>
                            <input type="text" name="rfid_uid" id="rfid_uid_input" required
                                class="w-full pl-10 pr-4 py-3 border-2 border-purple-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-mono text-lg cursor-text"
                                placeholder="👆 Klik di sini, lalu scan kartu..." autocomplete="off"
                                onfocus="onRfidInputFocus()"
                                onblur="onRfidInputBlur()">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">💡 Jika UID tidak muncul otomatis, klik kotak ini terlebih dahulu lalu tap kartu RFID</p>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto sm:text-sm transition-all shadow-purple-500/30">
                        <i class="fas fa-save mr-2 mt-0.5"></i> Simpan RFID
                    </button>
                    <button type="button" onclick="closeRfidModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 px-6 py-3 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rfidPollInterval = null;

    function onRfidInputFocus() {
        const statusBox = document.getElementById('rfid_focus_status');
        const dot = document.getElementById('rfid_focus_dot');
        const text = document.getElementById('rfid_focus_text');
        const icon = document.getElementById('rfid_icon');
        if (statusBox) {
            statusBox.className = 'flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 border border-green-300 transition-all';
            dot.className = 'inline-block w-3 h-3 rounded-full bg-green-500 flex-shrink-0 animate-pulse';
            text.className = 'text-sm font-medium text-green-700';
            text.textContent = '✅ Input aktif — silakan tap kartu RFID sekarang';
        }
        if (icon) icon.className = 'fas fa-wifi text-purple-500';
    }

    function onRfidInputBlur() {
        const statusBox = document.getElementById('rfid_focus_status');
        const dot = document.getElementById('rfid_focus_dot');
        const text = document.getElementById('rfid_focus_text');
        const icon = document.getElementById('rfid_icon');
        if (statusBox) {
            statusBox.className = 'flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 transition-all';
            dot.className = 'inline-block w-3 h-3 rounded-full bg-amber-400 flex-shrink-0';
            text.className = 'text-sm font-medium text-amber-700';
            text.textContent = '⚠️ Input tidak aktif — klik kotak scan lagi sebelum tap kartu';
        }
        if (icon) icon.className = 'fas fa-wifi text-gray-400';
    }

    function focusRfidInput() {
        const input = document.getElementById('rfid_uid_input');
        if (input) input.focus();
    }

    function openRfidModal(name, currentRfid, actionUrl, labelName = 'Nama') {
        document.getElementById('rfidModal').classList.remove('hidden');
        document.getElementById('rfid_label_name').textContent = labelName;
        document.getElementById('rfid_entity_name').value = name;
        document.getElementById('rfid_uid_input').value = currentRfid || '';
        document.getElementById('rfidForm').action = actionUrl;

        // Klik di dalam modal (selain tombol) = refocus ke input
        document.getElementById('rfidModal').addEventListener('click', function(e) {
            const isButton = e.target.closest('button') || e.target.closest('a');
            const isInput = e.target.closest('input');
            if (!isButton && !isInput) {
                focusRfidInput();
            }
        });
        
        // Focus dengan delay lebih panjang agar modal selesai render
        setTimeout(() => focusRfidInput(), 300);

        // Start polling scan buffer (untuk alat yang pakai mode API)
        startRfidPolling();
    }

    function closeRfidModal() {
        document.getElementById('rfidModal').classList.add('hidden');
        document.getElementById('rfidForm').reset();
        onRfidInputBlur(); // Reset indikator
        stopRfidPolling();
    }

    function startRfidPolling() {
        stopRfidPolling();
        rfidPollInterval = setInterval(async () => {
            try {
                const res = await fetch('{{ url("/api/rfid/scan-buffer") }}');
                const data = await res.json();
                if (data.uid) {
                    const input = document.getElementById('rfid_uid_input');
                    input.value = data.uid;
                    // Flash hijau untuk feedback
                    input.classList.add('border-green-500', 'bg-green-50');
                    onRfidInputFocus();
                    // Beep feedback
                    try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgir+8o3dMPFCKtLSibk5JaIG0r4xoTjpklLq0l3FNP1uIr7KdeFBIX4a2s5hvTTtgka+0l3BLPlyHr7adjk1BWoK4t5l0UEFjj7WwknBMQFeItLSeek5HY4u3s5hzTkBai7OzmHNPQ1uNubWXdE5CXIq4tJh0TkJckLi2k3FJPV2OubaVc01AXoy4tJdxTD9ejbu1lXJNP12NurWXc009Xoy4tZdyTUBdjru1l3NNP16MuLWXck1AXY27tZdzTT9ejLi1l3JNQF2Nu7WXc00/Xoy4tZdyTUBdjbu1l3NNQA==').play(); } catch(e) {}
                    setTimeout(() => {
                        input.classList.remove('border-green-500', 'bg-green-50');
                    }, 2000);
                }
            } catch (e) {
                console.error('RFID poll error:', e);
            }
        }, 1500);
    }

    function stopRfidPolling() {
        if (rfidPollInterval) {
            clearInterval(rfidPollInterval);
            rfidPollInterval = null;
        }
    }
</script>
@endpush
