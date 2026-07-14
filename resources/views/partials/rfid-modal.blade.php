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
    let rfidCurrentEntityName = ''; // Nama orang yang sedang dibuka modalnya
    let rfidCurrentEntityRfid = ''; // RFID UID yang sudah dimiliki orang ini (jika ada)

    // ================================================================
    //  KONVERSI DESIMAL → HEX (untuk USB Reader)
    //  USB Reader mengeluarkan UID dalam format desimal (contoh: 0035974332)
    //  ESP32+MFRC522 menggunakan format HEX uppercase (contoh: 02255ABC)
    //  Fungsi ini mendeteksi & mengkonversi agar UID yang disimpan konsisten
    // ================================================================

    function isDecimalUid(str) {
        // Desimal: hanya digit 0-9, panjang 8-12 karakter
        // HEX dari ESP32: hex chars (0-9, A-F), panjang 8 chars (4-byte) atau 14 chars (7-byte)
        if (!/^\d+$/.test(str)) return false;
        if (str.length < 6 || str.length > 12) return false;
        // Jika string punya huruf A-F → bukan desimal murni (sudah ditangani di atas)
        // Cek apakah nilainya masuk akal sebagai 4-byte (max 4294967295) atau 7-byte UID
        const num = parseInt(str, 10);
        if (isNaN(num)) return false;
        // 4-byte UID max = 0xFFFFFFFF = 4294967295
        // 7-byte UID max = sangat besar, tapi USB reader umumnya baca 4-byte
        return num > 0 && num <= 4294967295;
    }

    function decimalToHex(decimalStr) {
        const num = parseInt(decimalStr, 10);
        if (isNaN(num) || num <= 0) return decimalStr;
        // Pad ke 8 karakter (4 bytes) jika hasilnya kurang
        let hex = num.toString(16).toUpperCase();
        while (hex.length < 8) hex = '0' + hex;
        return hex;
    }

    function processRfidInput(inputValue) {
        const trimmed = inputValue.trim();
        if (!trimmed) return trimmed;

        const convertInfo = document.getElementById('rfid_convert_info');

        // Cek apakah input dari USB Reader (format desimal)
        if (isDecimalUid(trimmed)) {
            const hexUid = decimalToHex(trimmed);
            // Tampilkan info konversi
            convertInfo.classList.remove('hidden');
            document.getElementById('rfid_original_decimal').textContent = trimmed;
            document.getElementById('rfid_converted_hex').textContent = hexUid;
            return hexUid;
        }

        // Bukan desimal → sembunyikan info konversi, kembalikan uppercase
        convertInfo.classList.add('hidden');
        return trimmed.toUpperCase().replace(/\s+/g, '');
    }

    // ================================================================
    //  CEK KEPEMILIKAN UID (REAL-TIME)
    //  Mencegah UID yang sudah dimiliki seseorang disimpan ke orang lain
    // ================================================================

    function checkUidOwnership(uid) {
        if (!uid || uid.length < 4) {
            hideOwnerStatus();
            return;
        }

        // Jika UID sama dengan UID yang sudah dimiliki orang ini → skip cek
        if (rfidCurrentEntityRfid && uid === rfidCurrentEntityRfid) {
            showOwnerStatus('self');
            return;
        }

        showOwnerStatus('checking');

        // Debounce: tunggu 500ms setelah input terakhir
        if (rfidCheckTimeout) clearTimeout(rfidCheckTimeout);
        rfidCheckTimeout = setTimeout(async () => {
            try {
                const res = await fetch('{{ url("/api/rfid/check-uid") }}?uid=' + encodeURIComponent(uid));
                const data = await res.json();

                if (data.owned) {
                    // Cek apakah pemilik = orang yang sedang dibuka modalnya
                    if (data.owner_name === rfidCurrentEntityName) {
                        showOwnerStatus('self');
                    } else {
                        // UID sudah dimiliki orang LAIN
                        showOwnerStatus('owned', data.owner_name, data.owner_type);
                    }
                } else {
                    // UID tersedia
                    showOwnerStatus('available');
                }
            } catch (e) {
                console.error('Check UID error:', e);
                hideOwnerStatus();
            }
        }, 500);
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

    function enableSubmitButton() {
        const btn = document.getElementById('rfid_submit_btn');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed', 'from-gray-400', 'to-gray-500');
        btn.classList.add('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700');
    }

    function disableSubmitButton() {
        const btn = document.getElementById('rfid_submit_btn');
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed', 'from-gray-400', 'to-gray-500');
        btn.classList.remove('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700');
    }

    // ================================================================
    //  INPUT FOCUS / BLUR INDICATORS
    // ================================================================

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

    // ================================================================
    //  MODAL OPEN / CLOSE
    // ================================================================

    function openRfidModal(name, currentRfid, actionUrl, labelName = 'Nama') {
        rfidCurrentEntityName = name;
        rfidCurrentEntityRfid = (currentRfid || '').toUpperCase().trim();

        document.getElementById('rfidModal').classList.remove('hidden');
        document.getElementById('rfid_label_name').textContent = labelName;
        document.getElementById('rfid_entity_name').value = name;
        document.getElementById('rfid_uid_input').value = currentRfid || '';
        document.getElementById('rfidForm').action = actionUrl;

        // Reset status
        hideOwnerStatus();
        enableSubmitButton();
        document.getElementById('rfid_convert_info').classList.add('hidden');

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

        // Start polling scan buffer (untuk alat yang pakai mode API / ESP32 Station)
        startRfidPolling();

        // Setup listener untuk input dari USB Reader (keyboard emulator)
        setupUsbReaderListener();

        // Setup form submit handler (pastikan konversi sebelum submit)
        setupFormSubmitHandler();
    }

    function closeRfidModal() {
        document.getElementById('rfidModal').classList.add('hidden');
        document.getElementById('rfidForm').reset();
        onRfidInputBlur(); // Reset indikator
        stopRfidPolling();
        hideOwnerStatus();
        enableSubmitButton();
        document.getElementById('rfid_convert_info').classList.add('hidden');
        rfidCurrentEntityName = '';
        rfidCurrentEntityRfid = '';
    }

    // ================================================================
    //  USB READER LISTENER
    //  USB Reader bekerja sebagai keyboard emulator - mengetikkan UID
    //  dan biasanya diakhiri dengan Enter.
    //  Kita tangkap event input & keydown untuk:
    //  1. Deteksi format desimal → konversi ke HEX
    //  2. Cek kepemilikan UID secara real-time
    //  3. Mencegah form submit otomatis dari Enter key USB Reader
    //     sebelum pengecekan kepemilikan selesai
    // ================================================================

    function setupUsbReaderListener() {
        const input = document.getElementById('rfid_uid_input');
        
        // Hapus listener lama (mencegah duplikasi)
        input.removeEventListener('input', handleRfidInputChange);
        input.removeEventListener('keydown', handleRfidKeyDown);
        
        // Pasang listener baru
        input.addEventListener('input', handleRfidInputChange);
        input.addEventListener('keydown', handleRfidKeyDown);
    }

    function handleRfidInputChange(e) {
        const input = e.target;
        const rawValue = input.value.trim();
        
        if (!rawValue) {
            hideOwnerStatus();
            enableSubmitButton();
            document.getElementById('rfid_convert_info').classList.add('hidden');
            return;
        }

        // Proses konversi (desimal → hex jika perlu)
        const processedUid = processRfidInput(rawValue);
        
        // Jika ada konversi, update nilai input secara silent
        // (tanpa trigger event lagi)
        if (processedUid !== rawValue) {
            // Simpan posisi cursor
            input.value = processedUid;
        }

        // Cek kepemilikan UID
        checkUidOwnership(processedUid);
    }

    function handleRfidKeyDown(e) {
        // USB Reader biasanya mengirim Enter setelah UID
        if (e.key === 'Enter') {
            e.preventDefault(); // Cegah submit form otomatis

            const input = e.target;
            const rawValue = input.value.trim();
            
            if (!rawValue) return;

            // Proses konversi final
            const processedUid = processRfidInput(rawValue);
            input.value = processedUid;

            // Cek kepemilikan sebelum mengizinkan submit
            checkUidOwnership(processedUid);

            // Flash hijau untuk feedback bahwa scan berhasil diterima
            input.classList.add('border-green-500', 'bg-green-50');
            onRfidInputFocus();
            // Beep feedback
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
                    // Flash hijau untuk feedback
                    input.classList.add('border-green-500', 'bg-green-50');
                    onRfidInputFocus();
                    // Beep feedback
                    try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgir+8o3dMPFCKtLSibk5JaIG0r4xoTjpklLq0l3FNP1uIr7KdeFBIX4a2s5hvTTtgka+0l3BLPlyHr7adjk1BWoK4t5l0UEFjj7WwknBMQFeItLSeek5HY4u3s5hzTkBai7OzmHNPQ1uNubWXdE5CXIq4tJh0TkJckLi2k3FJPV2OubaVc01AXoy4tJdxTD9ejbu1lXJNP12NurWXc009Xoy4tZdyTUBdjru1l3NNP16MuLWXck1AXY27tZdzTT9ejLi1l3JNQF2Nu7WXc00/Xoy4tZdyTUBdjbu1l3NNQA==').play(); } catch(e) {}
                    setTimeout(() => {
                        input.classList.remove('border-green-500', 'bg-green-50');
                    }, 2000);

                    // Juga cek kepemilikan UID yang datang dari ESP32
                    checkUidOwnership(data.uid);
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
    //  Memastikan konversi desimal→HEX terjadi sebelum form dikirim
    //  Ini adalah "safety net" terakhir sebelum data masuk ke server
    // ================================================================

    function setupFormSubmitHandler() {
        const form = document.getElementById('rfidForm');

        // Hapus handler lama
        form.removeEventListener('submit', handleRfidFormSubmit);
        form.addEventListener('submit', handleRfidFormSubmit);
    }

    async function handleRfidFormSubmit(e) {
        e.preventDefault(); // Selalu cegah submit dulu, kita cek manual

        const input = document.getElementById('rfid_uid_input');
        const rawValue = input.value.trim();

        if (!rawValue) {
            input.reportValidity();
            return;
        }

        // Konversi final sebelum submit
        const processedUid = processRfidInput(rawValue);
        input.value = processedUid;

        // Cek apakah ini kartu yang sama dengan milik orang ini sendiri → boleh submit
        if (rfidCurrentEntityRfid && processedUid === rfidCurrentEntityRfid) {
            document.getElementById('rfidForm').submit();
            return;
        }

        // Cek kepemilikan SEGAR ke API (tidak pakai cache/state lama)
        try {
            const btn = document.getElementById('rfid_submit_btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengecek...';

            const res = await fetch('{{ url("/api/rfid/check-uid") }}?uid=' + encodeURIComponent(processedUid));
            const data = await res.json();

            if (data.owned) {
                // Cek apakah pemilik = orang yang sedang dibuka modalnya
                if (data.owner_name === rfidCurrentEntityName) {
                    // Milik orang ini sendiri → boleh submit
                    document.getElementById('rfidForm').submit();
                    return;
                }
                // Milik orang LAIN → BLOKIR
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-2 mt-0.5"></i> Simpan RFID';
                showOwnerStatus('owned', data.owner_name, data.owner_type);
                return;
            }

            // UID tersedia → submit form
            document.getElementById('rfidForm').submit();

        } catch (err) {
            console.error('Submit check error:', err);
            // Jika API gagal, tetap izinkan submit (backend punya validasi sendiri)
            document.getElementById('rfidForm').submit();
        }
    }
</script>
@endpush
