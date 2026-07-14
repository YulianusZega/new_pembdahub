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
                                onfocus="onRfidInputFocus(this)"
                                onblur="onRfidInputBlur()">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">💡 Jika UID tidak muncul otomatis, klik kotak ini terlebih dahulu lalu tap kartu RFID</p>
                    </div>

                    {{-- Info Konversi (muncul saat USB Reader mengirim format desimal) --}}
                    <div id="rfid_convert_info" class="hidden px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exchange-alt text-blue-500 mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-blue-800">Konversi Otomatis (USB Reader)</p>
                                <p class="text-blue-600 mt-1">
                                    Input asli: <code id="rfid_original_decimal" class="bg-blue-100 px-1 rounded font-mono">-</code>
                                </p>
                                <p class="text-blue-600">
                                    Dikonversi ke HEX: <code id="rfid_converted_hex" class="bg-blue-100 px-1 rounded font-mono font-bold">-</code>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Status Kepemilikan UID (cek real-time) --}}
                    <div id="rfid_owner_status" class="hidden">
                        {{-- Diisi via JavaScript --}}
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="submit" id="rfid_submit_btn" class="w-full inline-flex justify-center rounded-xl border border-transparent px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-base font-medium text-white shadow-sm hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto sm:text-sm transition-all shadow-purple-500/30">
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
    let rfidCheckTimeout = null;
    let rfidCurrentEntityName = '';
    let rfidCurrentEntityRfid = '';
    // Entity info untuk server-side self-detection (lebih akurat dari nama)
    let rfidExcludeType = '';  // 'student', 'employee', 'tefa_employee'
    let rfidExcludeId = '';    // ID entitas
    let rfidIsSubmitting = false; // Flag agar blur indicator tidak muncul saat submit

    // ================================================================
    //  KONVERSI DESIMAL → HEX (untuk USB Reader)
    // ================================================================

    function isDecimalUid(str) {
        if (!/^\d+$/.test(str)) return false;
        if (str.length < 6 || str.length > 12) return false;
        const num = parseInt(str, 10);
        if (isNaN(num)) return false;
        return num > 0 && num <= 4294967295;
    }

    function decimalToHex(decimalStr) {
        const num = parseInt(decimalStr, 10);
        if (isNaN(num) || num <= 0) return decimalStr;
        let hex = num.toString(16).toUpperCase();
        while (hex.length < 8) hex = '0' + hex;
        return hex;
    }

    function processRfidInput(inputValue) {
        const trimmed = inputValue.trim();
        if (!trimmed) return trimmed;

        const convertInfo = document.getElementById('rfid_convert_info');

        if (isDecimalUid(trimmed)) {
            const hexUid = decimalToHex(trimmed);
            convertInfo.classList.remove('hidden');
            document.getElementById('rfid_original_decimal').textContent = trimmed;
            document.getElementById('rfid_converted_hex').textContent = hexUid;
            return hexUid;
        }

        convertInfo.classList.add('hidden');
        return trimmed.toUpperCase().replace(/\s+/g, '');
    }

    // ================================================================
    //  CEK KEPEMILIKAN UID (REAL-TIME) — hanya untuk feedback visual
    //  Pengecekan definitif dilakukan saat submit form
    // ================================================================

    function checkUidOwnershipNow(uid) {
        // Langsung cek ke API tanpa debounce (dipanggil saat Enter / scan selesai)
        if (!uid || uid.length < 6) {
            hideOwnerStatus();
            return;
        }

        showOwnerStatus('checking');

        // Bangun URL dengan parameter exclude untuk self-detection server-side
        let url = '{{ url("/api/rfid/check-uid") }}?uid=' + encodeURIComponent(uid);
        if (rfidExcludeType && rfidExcludeId) {
            url += '&exclude_type=' + encodeURIComponent(rfidExcludeType);
            url += '&exclude_id=' + encodeURIComponent(rfidExcludeId);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.owned) {
                    showOwnerStatus('owned', data.owner_name, data.owner_type);
                } else if (data.is_self) {
                    showOwnerStatus('self');
                } else {
                    showOwnerStatus('available');
                }
            })
            .catch(err => {
                console.error('Check UID error:', err);
                hideOwnerStatus();
            });
    }

    function showOwnerStatus(status, ownerName, ownerType) {
        const container = document.getElementById('rfid_owner_status');
        container.classList.remove('hidden');

        if (status === 'checking') {
            container.innerHTML = `
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    <p class="text-sm text-gray-500">Mengecek kepemilikan kartu...</p>
                </div>
            `;
        } else if (status === 'owned') {
            container.innerHTML = `
                <div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-red-50 border-2 border-red-300">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 text-lg"></i>
                    <div>
                        <p class="font-bold text-red-800 text-sm">⛔ KARTU SUDAH TERDAFTAR!</p>
                        <p class="text-red-700 text-sm mt-1">Kartu ini milik <strong>${ownerName}</strong> (${ownerType})</p>
                        <p class="text-red-600 text-xs mt-1">Tidak bisa menyimpan kartu ini ke orang lain. Gunakan kartu yang berbeda.</p>
                    </div>
                </div>
            `;
        } else if (status === 'self') {
            container.innerHTML = `
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
                    <i class="fas fa-user-check text-blue-500"></i>
                    <p class="text-sm font-medium text-blue-700">ℹ️ Kartu ini sudah terdaftar untuk orang ini — bisa di-update</p>
                </div>
            `;
        } else if (status === 'available') {
            container.innerHTML = `
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 border border-green-300">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <p class="text-sm font-medium text-green-700">✅ Kartu tersedia — belum dimiliki siapapun</p>
                </div>
            `;
        }
    }

    function hideOwnerStatus() {
        const container = document.getElementById('rfid_owner_status');
        container.classList.add('hidden');
        container.innerHTML = '';
    }

    // ================================================================
    //  INPUT FOCUS / BLUR INDICATORS
    // ================================================================

    function onRfidInputFocus(el) {
        // Select all text so that a new scan overwrites the existing value
        if (el && typeof el.select === 'function') {
            el.select();
        } else {
            const input = document.getElementById('rfid_uid_input');
            if (input) input.select();
        }

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
        // Jangan tampilkan warning blur saat sedang submit atau saat input sudah terisi
        if (rfidIsSubmitting) return;

        const input = document.getElementById('rfid_uid_input');
        const statusBox = document.getElementById('rfid_focus_status');
        const dot = document.getElementById('rfid_focus_dot');
        const text = document.getElementById('rfid_focus_text');
        const icon = document.getElementById('rfid_icon');

        if (input && input.value.trim().length > 0) {
            // Input sudah terisi (kartu sudah terscan) → tampilkan status positif
            if (statusBox) {
                statusBox.className = 'flex items-center gap-3 px-4 py-3 rounded-xl bg-purple-50 border border-purple-200 transition-all';
                dot.className = 'inline-block w-3 h-3 rounded-full bg-purple-500 flex-shrink-0';
                text.className = 'text-sm font-medium text-purple-700';
                text.textContent = '📋 Kartu terdeteksi — klik Simpan RFID untuk menyimpan';
            }
            if (icon) icon.className = 'fas fa-wifi text-purple-500';
        } else {
            // Input kosong → tampilkan warning biasa
            if (statusBox) {
                statusBox.className = 'flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 transition-all';
                dot.className = 'inline-block w-3 h-3 rounded-full bg-amber-400 flex-shrink-0';
                text.className = 'text-sm font-medium text-amber-700';
                text.textContent = '⚠️ Input tidak aktif — klik kotak scan lagi sebelum tap kartu';
            }
            if (icon) icon.className = 'fas fa-wifi text-gray-400';
        }
    }

    function focusRfidInput() {
        const input = document.getElementById('rfid_uid_input');
        if (input) {
            input.focus();
            // Gunakan timeout kecil untuk memastikan select terjadi setelah focus event selesai
            setTimeout(() => input.select(), 50);
        }
    }

    // ================================================================
    //  MODAL OPEN / CLOSE
    //  Parameter baru: entityType ('student'|'employee'|'tefa_employee')
    //                   entityId   (ID database)
    //  Backward-compatible: jika tidak dikirim, tetap jalan normal
    // ================================================================

    function openRfidModal(name, currentRfid, actionUrl, labelName, entityType, entityId) {
        rfidCurrentEntityName = name;
        rfidCurrentEntityRfid = (currentRfid || '').toUpperCase().trim();
        rfidExcludeType = entityType || '';
        rfidExcludeId = entityId || '';
        rfidIsSubmitting = false;

        document.getElementById('rfidModal').classList.remove('hidden');
        document.getElementById('rfid_label_name').textContent = labelName || 'Nama';
        document.getElementById('rfid_entity_name').value = name;
        document.getElementById('rfid_uid_input').value = currentRfid || '';
        document.getElementById('rfidForm').action = actionUrl;

        // Reset status
        hideOwnerStatus();
        document.getElementById('rfid_convert_info').classList.add('hidden');

        // Klik di dalam modal (selain tombol) = refocus ke input
        document.getElementById('rfidModal').addEventListener('click', function(e) {
            const isButton = e.target.closest('button') || e.target.closest('a');
            const isInput = e.target.closest('input');
            if (!isButton && !isInput) {
                focusRfidInput();
            }
        });
        
        // Focus dengan delay agar modal selesai render
        setTimeout(() => focusRfidInput(), 300);

        // Start polling scan buffer (untuk ESP32 Station via API)
        startRfidPolling();

        // Setup listener untuk USB Reader (keyboard emulator)
        setupUsbReaderListener();

        // Setup form submit handler
        setupFormSubmitHandler();
    }

    function closeRfidModal() {
        document.getElementById('rfidModal').classList.add('hidden');
        document.getElementById('rfidForm').reset();
        onRfidInputBlur();
        stopRfidPolling();
        hideOwnerStatus();
        document.getElementById('rfid_convert_info').classList.add('hidden');
        rfidCurrentEntityName = '';
        rfidCurrentEntityRfid = '';
        rfidExcludeType = '';
        rfidExcludeId = '';
        rfidIsSubmitting = false;
    }

    // ================================================================
    //  USB READER LISTENER
    //  USB Reader = keyboard emulator, mengetik UID lalu Enter.
    //  PENTING: Jangan cek kepemilikan per karakter (partial UID = salah).
    //  Hanya cek saat: Enter key diterima ATAU input stabil 1.5 detik.
    // ================================================================

    function setupUsbReaderListener() {
        const input = document.getElementById('rfid_uid_input');
        input.removeEventListener('input', handleRfidInputChange);
        input.removeEventListener('keydown', handleRfidKeyDown);
        input.addEventListener('input', handleRfidInputChange);
        input.addEventListener('keydown', handleRfidKeyDown);
    }

    function handleRfidInputChange(e) {
        const input = e.target;
        const rawValue = input.value.trim();
        
        if (!rawValue) {
            hideOwnerStatus();
            document.getElementById('rfid_convert_info').classList.add('hidden');
            return;
        }

        // Proses konversi desimal → hex
        const processedUid = processRfidInput(rawValue);
        if (processedUid !== rawValue) {
            input.value = processedUid;
        }

        // DEBOUNCE PANJANG: Hanya cek setelah 1.5 detik stabil (jaga-jaga USB Reader lambat)
        // Pengecekan utama tetap di Enter key handler dan submit handler
        if (rfidCheckTimeout) clearTimeout(rfidCheckTimeout);
        rfidCheckTimeout = setTimeout(() => {
            if (processedUid.length >= 6) {
                checkUidOwnershipNow(processedUid);
            }
        }, 1500);
    }

    function handleRfidKeyDown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const input = e.target;
            const rawValue = input.value.trim();
            if (!rawValue) return;

            // Konversi final
            const processedUid = processRfidInput(rawValue);
            input.value = processedUid;

            // Cancel debounce yang pending, langsung cek SEKARANG
            if (rfidCheckTimeout) clearTimeout(rfidCheckTimeout);
            checkUidOwnershipNow(processedUid);

            // Flash hijau untuk feedback scan berhasil
            input.classList.add('border-green-500', 'bg-green-50');
            onRfidInputFocus();
            try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgir+8o3dMPFCKtLSibk5JaIG0r4xoTjpklLq0l3FNP1uIr7KdeFBIX4a2s5hvTTtgka+0l3BLPlyHr7adjk1BWoK4t5l0UEFjj7WwknBMQFeItLSeek5HY4u3s5hzTkBai7OzmHNPQ1uNubWXdE5CXIq4tJh0TkJckLi2k3FJPV2OubaVc01AXoy4tJdxTD9ejbu1lXJNP12NurWXc009Xoy4tZdyTUBdjru1l3NNP16MuLWXck1AXY27tZdzTT9ejLi1l3JNQF2Nu7WXc00/Xoy4tZdyTUBdjbu1l3NNQA==').play(); } catch(err) {}
            setTimeout(() => {
                input.classList.remove('border-green-500', 'bg-green-50');
            }, 2000);
        }
    }

    // ================================================================
    //  POLLING SCAN BUFFER (untuk ESP32 Station via API)
    //  Tidak berubah - tetap berfungsi seperti sebelumnya
    // ================================================================

    function startRfidPolling() {
        stopRfidPolling();
        rfidPollInterval = setInterval(async () => {
            try {
                const res = await fetch('{{ url("/api/rfid/scan-buffer") }}');
                const data = await res.json();
                if (data.uid) {
                    const input = document.getElementById('rfid_uid_input');
                    input.value = data.uid;
                    input.classList.add('border-green-500', 'bg-green-50');
                    onRfidInputFocus();
                    try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgir+8o3dMPFCKtLSibk5JaIG0r4xoTjpklLq0l3FNP1uIr7KdeFBIX4a2s5hvTTtgka+0l3BLPlyHr7adjk1BWoK4t5l0UEFjj7WwknBMQFeItLSeek5HY4u3s5hzTkBai7OzmHNPQ1uNubWXdE5CXIq4tJh0TkJckLi2k3FJPV2OubaVc01AXoy4tJdxTD9ejbu1lXJNP12NurWXc009Xoy4tZdyTUBdjru1l3NNP16MuLWXck1AXY27tZdzTT9ejLi1l3JNQF2Nu7WXc00/Xoy4tZdyTUBdjbu1l3NNQA==').play(); } catch(e) {}
                    setTimeout(() => {
                        input.classList.remove('border-green-500', 'bg-green-50');
                    }, 2000);
                    // Cek kepemilikan UID dari ESP32
                    checkUidOwnershipNow(data.uid);
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

    // ================================================================
    //  FORM SUBMIT HANDLER
    //  Melakukan cek SEGAR ke API sebelum submit.
    //  Server-side self-detection via exclude_type + exclude_id.
    // ================================================================

    function setupFormSubmitHandler() {
        const form = document.getElementById('rfidForm');
        form.removeEventListener('submit', handleRfidFormSubmit);
        form.addEventListener('submit', handleRfidFormSubmit);
    }

    async function handleRfidFormSubmit(e) {
        e.preventDefault();
        rfidIsSubmitting = true;

        const input = document.getElementById('rfid_uid_input');
        const rawValue = input.value.trim();

        if (!rawValue) {
            input.reportValidity();
            rfidIsSubmitting = false;
            return;
        }

        // Konversi final
        const processedUid = processRfidInput(rawValue);
        input.value = processedUid;

        // Cek kepemilikan SEGAR ke API
        try {
            const btn = document.getElementById('rfid_submit_btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengecek...';

            let url = '{{ url("/api/rfid/check-uid") }}?uid=' + encodeURIComponent(processedUid);
            if (rfidExcludeType && rfidExcludeId) {
                url += '&exclude_type=' + encodeURIComponent(rfidExcludeType);
                url += '&exclude_id=' + encodeURIComponent(rfidExcludeId);
            }

            const res = await fetch(url);
            const data = await res.json();

            if (data.owned) {
                // Milik orang LAIN → BLOKIR
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-2 mt-0.5"></i> Simpan RFID';
                showOwnerStatus('owned', data.owner_name, data.owner_type);
                rfidIsSubmitting = false;
                return;
            }

            // UID tersedia atau milik sendiri → submit
            document.getElementById('rfidForm').submit();

        } catch (err) {
            console.error('Submit check error:', err);
            // API gagal → fallback ke backend validation
            document.getElementById('rfidForm').submit();
        }
    }
</script>
@endpush
