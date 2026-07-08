<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PembdaHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 min-h-screen">
    <nav class="bg-white shadow-lg border-b-4 border-emerald-500">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                    P
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">PembdaHub</h1>
                    <p class="text-xs text-gray-500">Penerimaan Siswa Baru</p>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">
            {{-- Success Animation --}}
            <div class="text-center mb-8">
                <div class="inline-block w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-6xl shadow-2xl animate-bounce mb-4">
                    ✅
                </div>
                <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 mb-2">
                    Pendaftaran Berhasil!
                </h1>
                <p class="text-lg text-gray-600">Terima kasih telah mendaftar di PembdaHub</p>
            </div>

            {{-- Registration Info Card --}}
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6 border-t-4 border-emerald-500">
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-500 mb-2">Nomor Registrasi Anda</p>
                    <div class="inline-block bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-8 py-4 rounded-xl text-3xl font-bold shadow-lg">
                        {{ $applicant->registration_number }}
                    </div>
                    <p class="text-xs text-gray-500 mt-3">
                        ⚠️ <strong>PENTING:</strong> Simpan nomor ini untuk cek status pendaftaran
                    </p>
                </div>
                
                {{-- Email & WA Notification --}}
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-4 border-2 border-green-200">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-green-800 mb-1">✉️ Konfirmasi Telah Dikirim!</p>
                            <p class="text-sm text-green-700">
                                Email konfirmasi dengan nomor registrasi dan instruksi pembayaran telah dikirim ke <strong>{{ $applicant->email }}</strong>
                            </p>
                            <p class="text-sm text-green-700 mt-1">
                                📱 Notifikasi WhatsApp juga dikirim ke <strong>{{ $applicant->phone }}</strong>
                            </p>
                            <p class="text-xs text-green-600 mt-2">
                                <i class="fas fa-info-circle"></i> Jika tidak menerima dalam 5 menit, periksa folder spam atau hubungi panitia
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-bold text-gray-800 mb-4">📋 Data Pendaftaran:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nama Lengkap</p>
                            <p class="font-semibold text-gray-800">{{ $applicant->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">NISN</p>
                            <p class="font-semibold text-gray-800">{{ $applicant->nisn }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Sekolah Tujuan</p>
                            <p class="font-semibold text-gray-800">{{ $applicant->school->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jalur</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($applicant->admission_path) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Next Steps - BERBEDA untuk Prestasi vs Reguler --}}
            @if($applicant->admission_path === 'prestasi')
                {{-- JALUR PRESTASI: Fokus pada Verifikasi Dokumen Prestasi --}}
                <div class="bg-gradient-to-br from-purple-50 to-indigo-100 rounded-2xl p-6 mb-6 border-2 border-purple-300">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-4xl">🏆</span>
                            <div>
                                <h3 class="font-bold text-xl">Jalur Prestasi</h3>
                                <p class="text-sm opacity-90">Anda mendaftar melalui jalur prestasi dengan pembebasan biaya pendaftaran</p>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="text-2xl mr-2">🎯</span>
                        Langkah Selanjutnya:
                    </h3>
                    <ol class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">1</span>
                            <div>
                                <p class="font-semibold text-gray-800">Verifikasi Dokumen Prestasi</p>
                                <p class="text-sm text-gray-600">Panitia akan memverifikasi bukti prestasi (Raport/Piagam Juara Kelas) yang Anda upload. <strong>Estimasi: 2-3 hari kerja</strong></p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">2</span>
                            <div>
                                <p class="font-semibold text-gray-800">Notifikasi Hasil Verifikasi</p>
                                <p class="text-sm text-gray-600">Anda akan menerima email/WhatsApp jika dokumen prestasi <strong class="text-green-600">✅ DISETUJUI</strong> atau <strong class="text-red-600">❌ DITOLAK</strong></p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">3</span>
                            <div>
                                <p class="font-semibold text-gray-800">Upload Dokumen Lengkap</p>
                                <p class="text-sm text-gray-600">Upload dokumen wajib: 
                                    <strong>{{ implode(', ', $applicant->school->getRequiredDocumentLabels()) }}</strong> 
                                    melalui link yang dikirim via WhatsApp.
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">4</span>
                            <div>
                                @if($applicant->school->requires_test)
                                    <p class="font-semibold text-gray-800">Ikuti Tes Masuk ({{ $applicant->school->test_type ?? 'CBT' }})</p>
                                    <p class="text-sm text-gray-600">Jadwal tes akan diinformasikan via email/WhatsApp setelah dokumen lengkap terverifikasi.</p>
                                @else
                                    <p class="font-semibold text-gray-800">Verifikasi Berkas Akhir</p>
                                    <p class="text-sm text-gray-600">Setelah dokumen lengkap, panitia akan melakukan verifikasi akhir sebelum pengumuman.</p>
                                @endif
                            </div>
                        </li>
                    </ol>
                    
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg mt-4">
                        <p class="font-bold text-green-800 mb-2">💰 BEBAS BIAYA PENDAFTARAN!</p>
                        <p class="text-sm text-green-700">Karena Anda mendaftar melalui jalur prestasi, Anda <strong>TIDAK PERLU</strong> membayar biaya pendaftaran. Panitia akan langsung memproses verifikasi dokumen prestasi Anda.</p>
                    </div>
                </div>
            @else
                {{-- JALUR REGULER: Fokus pada Pembayaran --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 mb-6 border-2 border-blue-200">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-4xl">📝</span>
                            <div>
                                <h3 class="font-bold text-xl">Jalur Reguler</h3>
                                <p class="text-sm opacity-90">Anda mendaftar melalui jalur reguler</p>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="text-2xl mr-2">🎯</span>
                        Langkah Selanjutnya:
                    </h3>
                    <ol class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</span>
                            <div>
                                <p class="font-semibold text-gray-800">Lakukan Pembayaran</p>
                                <p class="text-sm text-gray-600">Biaya pendaftaran <strong>Rp {{ number_format(($applicant->school_id == 3 ? 300000 : 50000), 0, ',', '.') }}</strong>. Transfer ke rekening yang tertera di bawah.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</span>
                            <div>
                                <p class="font-semibold text-gray-800">Upload Bukti Pembayaran</p>
                                <p class="text-sm text-gray-600">Via WhatsApp ke nomor panitia yang tertera.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</span>
                            <div>
                                <p class="font-semibold text-gray-800">Siapkan Dokumen</p>
                                <p class="text-sm text-gray-600">Siapkan: <strong>{{ implode(', ', $applicant->school->getRequiredDocumentLabels()) }}</strong>. Upload setelah pembayaran diverifikasi.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</span>
                            <div>
                                @if($applicant->school->requires_test)
                                    <p class="font-semibold text-gray-800">Ikuti Tes Masuk ({{ $applicant->school->test_type ?? 'CBT' }})</p>
                                    <p class="text-sm text-gray-600">Jadwal tes akan diinformasikan via WhatsApp/SMS segera setelah berkas Anda diverifikasi.</p>
                                @else
                                    <p class="font-semibold text-gray-800">Pengumuman Kelulusan</p>
                                    <p class="text-sm text-gray-600">Hasil pendaftaran akan diumumkan setelah semua berkas diverifikasi oleh Panitia.</p>
                                @endif
                            </div>
                        </li>
                    </ol>
                </div>
            @endif

            {{-- Payment Info - HANYA untuk Jalur Reguler --}}
            @if($applicant->admission_path !== 'prestasi')
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 mb-6 border-2 border-yellow-300 shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💳</span>
                        Informasi Pembayaran:
                    </h3>
                    
                    <div class="bg-white rounded-xl p-5 mb-4 border-2 border-yellow-200">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-gray-700">Biaya Pendaftaran</p>
                            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format(($applicant->school_id == 3 ? 300000 : 50000), 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <p class="text-xs text-blue-700 mb-1 font-black uppercase tracking-widest"><i class="fas fa-university mr-1"></i> Rekening Transfer</p>
                            <p class="text-sm font-bold text-blue-900 mb-0.5">BANK MANDIRI</p>
                            <p class="text-2xl font-black text-blue-600 tracking-wider">1070010418269</p>
                            <p class="text-[11px] font-bold text-blue-800 mt-1 uppercase">a.n. PENGURUS YAYASAN PERGURUAN PEMBDA NIAS</p>
                        </div>
                    </div>

                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-lg mb-4">
                        <p class="font-bold text-orange-800 mb-2"><i class="fas fa-exclamation-triangle"></i> Cara Konfirmasi Pembayaran:</p>
                        <ol class="text-sm text-orange-700 space-y-1">
                            <li>1️⃣ Transfer sesuai nominal yang tertera</li>
                            <li>2️⃣ Foto/Screenshot bukti transfer</li>
                            <li>3️⃣ Kirim ke salah satu channel berikut dengan menyertakan <strong>Nomor Registrasi</strong>:</li>
                        </ol>
                        <div class="mt-3 space-y-2">
                            <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">WhatsApp Panitia ({{ $applicant->school->psb_contact_person }})</p>
                                    <p class="font-bold text-gray-800">{{ $applicant->school->psb_contact_phone }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Email Panitia</p>
                                    <p class="font-bold text-gray-800">{{ $applicant->school->email ?? 'psb@pembdanias.sch.id' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-xs text-green-700">
                            <i class="fas fa-clock"></i> <strong>Verifikasi:</strong> Pembayaran akan diverifikasi dalam 1x24 jam (hari kerja)
                        </p>
                    </div>
                </div>
            @endif

            {{-- Notification Timeline --}}
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 mb-6 border-2 border-indigo-200 shadow-lg">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <span class="text-2xl mr-2">📬</span>
                    Yang Akan Anda Terima:
                </h3>
                <div class="space-y-3">
                    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">✅</span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">1. Konfirmasi Pendaftaran (Sudah Dikirim)</p>
                                <p class="text-sm text-gray-600">Email & WhatsApp berisi nomor registrasi + instruksi {{ $applicant->admission_path === 'prestasi' ? 'verifikasi dokumen prestasi' : 'pembayaran' }}</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-check"></i> Selesai - Periksa inbox Anda</p>
                            </div>
                        </div>
                    </div>
                    @if($applicant->admission_path === 'prestasi')
                        <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">🏆</span>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">2. Hasil Verifikasi Dokumen Prestasi</p>
                                    <p class="text-sm text-gray-600">Setelah panitia memverifikasi bukti prestasi (2-3 hari kerja)</p>
                                    <p class="text-xs text-purple-600 mt-1"><i class="fas fa-hourglass-half"></i> Menunggu - Tim sedang memeriksa dokumen Anda</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">💳</span>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">2. Konfirmasi Pembayaran</p>
                                    <p class="text-sm text-gray-600">Setelah transfer diverifikasi (1x24 jam)</p>
                                    <p class="text-xs text-blue-600 mt-1"><i class="fas fa-hourglass-half"></i> Menunggu - Lakukan pembayaran terlebih dahulu</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded-xl p-4 border-l-4 border-teal-500">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">📄</span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">3. Instruksi Upload Dokumen</p>
                                <p class="text-sm text-gray-600">Panduan untuk upload KK, Akta, Ijazah, Foto</p>
                                <p class="text-xs text-teal-600 mt-1"><i class="fas fa-clock"></i> Akan datang setelah {{ $applicant->admission_path === 'prestasi' ? 'dokumen prestasi disetujui' : 'pembayaran terverifikasi' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border-l-4 border-orange-500">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">📝</span>
                            <div class="flex-1">
                                @if($applicant->school->requires_test)
                                    <p class="font-bold text-gray-800">4. Jadwal Tes Masuk ({{ $applicant->school->test_type ?? 'CBT' }})</p>
                                    <p class="text-sm text-gray-600">Informasi via WhatsApp/SMS dikirim H-3 sebelum jadwal tes dilaksanakan.</p>
                                    <p class="text-xs text-orange-600 mt-1"><i class="fas fa-calendar"></i> Segera setelah berkas lengkap</p>
                                @else
                                    <p class="font-bold text-gray-800">4. Verifikasi Akhir Panitia</p>
                                    <p class="text-sm text-gray-600">Panitia melakukan pengecekan akhir terhadap seluruh berkas dan data pendaftar.</p>
                                    <p class="text-xs text-blue-600 mt-1"><i class="fas fa-check-double"></i> Tahap Akhir</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border-l-4 border-pink-500">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">🎉</span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">5. Pengumuman Hasil</p>
                                <p class="text-sm text-gray-600">Diberitahukan secara resmi melalui portal pendaftaran dan WhatsApp.</p>
                                <p class="text-xs text-pink-600 mt-1"><i class="fas fa-trophy"></i> Menunggu Jadwal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 mb-6 border-2 border-gray-300">
                <h3 class="font-bold text-gray-800 mb-4 text-center flex items-center justify-center gap-2">
                    <span class="text-xl">📞</span> Butuh Bantuan?
                </h3>
                <p class="text-center text-gray-600 text-sm mb-4">
                    Jika mengalami kendala atau ada pertanyaan, hubungi Panitia PSB:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    @php
                        $waPhone = str_replace(['+', '-', ' ', '(', ')'], '', $applicant->school->psb_contact_phone ?? '081260932084');
                        if (str_starts_with($waPhone, '0')) {
                            $waPhone = '62' . substr($waPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text=Halo%20Panitia%20PSB%20{{ urlencode($applicant->school->name) }},%20saya%20{{ urlencode($applicant->full_name) }}%20ingin%20bertanya%20terkait%20pendaftaran%20PSB." target="_blank" class="bg-white rounded-xl p-4 hover:shadow-lg transition-all border-2 border-green-200 hover:border-green-400">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white text-xl">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <p class="font-bold text-gray-800 text-center">WhatsApp<br><span class="text-[10px] text-gray-500 font-normal">({{ $applicant->school->psb_contact_person ?? 'Panitia' }})</span></p>
                            <p class="text-xs text-gray-600">{{ $applicant->school->psb_contact_phone ?? '0812-xxxx-xxxx' }}</p>
                        </div>
                    </a>
                    <a href="tel:{{ $applicant->school->psb_contact_phone }}" class="bg-white rounded-xl p-4 hover:shadow-lg transition-all border-2 border-blue-200 hover:border-blue-400">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl">
                                <i class="fas fa-phone"></i>
                            </div>
                            <p class="font-bold text-gray-800">Telepon</p>
                            <p class="text-xs text-gray-600">{{ $applicant->school->psb_contact_phone }}</p>
                        </div>
                    </a>
                    <a href="mailto:{{ $applicant->school->email ?? 'psb@pembdanias.sch.id' }}" class="bg-white rounded-xl p-4 hover:shadow-lg transition-all border-2 border-purple-200 hover:border-purple-400">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white text-xl">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <p class="font-bold text-gray-800">Email</p>
                            <p class="text-xs text-gray-600">{{ $applicant->school->email ?? 'psb@pembdanias.sch.id' }}</p>
                        </div>
                    </a>
                </div>
                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-lg">
                    <p class="text-xs text-yellow-800">
                        <i class="fas fa-clock"></i> <strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 16:00 WIB | Sabtu, 08:00 - 12:00 WIB
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col md:flex-row gap-4">
                <a href="{{ route('public.registration.check') }}" class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-bold text-center hover:shadow-xl transition-all">
                    <i class="fas fa-search mr-2"></i>Cek Status Pendaftaran
                </a>
                <a href="{{ route('public.registration.index') }}" class="flex-1 px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold text-center hover:bg-gray-200 transition-all">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">© 2026 PembdaHub - Yayasan Pembangunan Daerah</p>
        </div>
    </footer>
</body>
</html>

