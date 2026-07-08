<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status: {{ $applicant->full_name }} - PembdaHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <nav class="bg-white shadow-lg border-b-4 border-emerald-500">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                        P
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">PembdaHub</h1>
                        <p class="text-xs text-gray-500">Status Pendaftaran</p>
                    </div>
                </div>
                <a href="{{ route('public.registration.check') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all text-sm font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Cek Lagi
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        {{-- Student Info Card --}}
        <div class="max-w-5xl mx-auto mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ substr($applicant->full_name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $applicant->full_name }}</h2>
                        <p class="text-sm text-gray-600">{{ $applicant->school->name }} • {{ $applicant->academicYear->year }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">
                                📋 {{ $applicant->registration_number }}
                            </span>
                            <span class="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-semibold">
                                🆔 {{ $applicant->nisn }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VISUAL FLOW TRACKER (Use Component) --}}
        <div class="max-w-5xl mx-auto">
            @php
                // Create a temporary object for the component
                $flowApplicant = $applicant;
            @endphp
            
            <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl shadow-lg p-8 mb-6 border-2 border-white">
                <h3 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 mb-8 text-center">
                    🎯 Perjalanan Pendaftaran Anda
                </h3>

                <div class="relative">
                    <div class="absolute top-0 left-1/4 w-32 h-32 bg-gradient-to-br from-blue-200 to-purple-200 rounded-full opacity-20 blur-2xl"></div>
                    <div class="absolute top-0 right-1/4 w-40 h-40 bg-gradient-to-br from-pink-200 to-orange-200 rounded-full opacity-20 blur-2xl"></div>
                    
                    <div class="absolute top-20 left-0 w-full h-2 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 rounded-full shadow-inner"></div>
                    
                    @php
                        $statuses = ['draft', 'submitted', 'payment_verified', 'prestasi_verified', 'document_verified', 'tested', 'scored', 'accepted', 'rejected', 'reregistered', 'registered'];
                        $currentIndex = array_search($applicant->status, $statuses);
                        $progress = ($currentIndex / (count($statuses) - 1)) * 100;
                    @endphp
                    <div class="absolute top-20 left-0 h-2 bg-gradient-to-r from-emerald-400 via-teal-500 to-cyan-500 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: {{ $progress }}%">
                        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-lg animate-pulse"></div>
                    </div>

                    <div class="relative flex justify-between pt-2">
                        @php
                            $steps = [
                                ['key' => 'draft', 'emoji' => '✏️', 'label' => 'Draft', 'desc' => 'Formulir diisi', 'color' => 'from-gray-400 to-gray-500'],
                                ['key' => 'submitted', 'emoji' => '📨', 'label' => 'Submitted', 'desc' => 'Menunggu Verifikasi', 'color' => 'from-blue-400 to-blue-600'],
                                ['key' => $applicant->admission_path === 'prestasi' ? 'prestasi_verified' : 'payment_verified', 'emoji' => $applicant->admission_path === 'prestasi' ? '🏆' : '💰', 'label' => $applicant->admission_path === 'prestasi' ? 'Prestasi OK' : 'Bayar OK', 'desc' => $applicant->admission_path === 'prestasi' ? 'Prestasi Verified' : 'Pembayaran Verified', 'color' => 'from-indigo-400 to-indigo-600'],
                                ['key' => 'document_verified', 'emoji' => '📋', 'label' => 'Dokumen OK', 'desc' => 'Dokumen Verified', 'color' => 'from-purple-400 to-purple-600'],
                                ['key' => 'tested', 'emoji' => '✍️', 'label' => 'Sudah Tes', 'desc' => 'Tes Selesai', 'color' => 'from-pink-400 to-pink-600'],
                                ['key' => 'scored', 'emoji' => '📈', 'label' => 'Dinilai', 'desc' => 'Nilai Keluar', 'color' => 'from-orange-400 to-orange-600'],
                                ['key' => 'accepted', 'emoji' => '🎊', 'label' => 'DITERIMA!', 'desc' => 'Selamat!', 'color' => 'from-green-400 to-green-600'],
                                ['key' => 'reregistered', 'emoji' => '💵', 'label' => 'Daftar Ulang', 'desc' => 'Bayar Pangkal', 'color' => 'from-teal-400 to-teal-600'],
                                ['key' => 'registered', 'emoji' => '🎓', 'label' => 'Siswa Aktif', 'desc' => 'Resmi Siswa', 'color' => 'from-emerald-400 to-emerald-600'],
                            ];
                        @endphp

                        @foreach($steps as $index => $step)
                            @php
                                $isPassed = array_search($step['key'], $statuses) <= $currentIndex;
                                $isCurrent = $step['key'] === $applicant->status;
                            @endphp

                            <div class="flex flex-col items-center relative group z-10">
                                <div class="relative transform transition-all duration-300 hover:scale-110">
                                    @if($isCurrent)
                                        <div class="absolute inset-0 rounded-full bg-gradient-to-r {{ $step['color'] }} opacity-50 blur-xl animate-pulse"></div>
                                    @endif
                                    
                                    <div class="relative w-16 h-16 rounded-2xl flex flex-col items-center justify-center text-2xl transition-all duration-500 shadow-2xl
                                        {{ $isPassed ? 'bg-gradient-to-br ' . $step['color'] . ' text-white' : 'bg-white text-gray-400 border-2 border-gray-200' }}
                                        {{ $isCurrent ? 'ring-4 ring-offset-2 ring-offset-white scale-125' : '' }}">
                                        
                                        <div class="{{ $isCurrent ? 'animate-bounce' : '' }}">
                                            {{ $step['emoji'] }}
                                        </div>
                                        
                                        @if($isPassed && !$isCurrent)
                                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    @if($isCurrent)
                                        <div class="absolute -top-2 -right-2 text-yellow-400 text-xl animate-spin">✨</div>
                                    @endif
                                </div>

                                <div class="mt-4 text-center max-w-[80px]">
                                    <p class="text-xs font-bold {{ $isPassed ? 'text-transparent bg-clip-text bg-gradient-to-r ' . $step['color'] : 'text-gray-400' }}">
                                        {{ $step['label'] }}
                                    </p>
                                    <p class="text-[9px] {{ $isPassed ? 'text-gray-600' : 'text-gray-400' }} mt-1 hidden lg:block leading-tight">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t-2 border-white">
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 border-2 border-emerald-200">
                        <div class="flex items-start gap-3">
                            <div class="text-3xl">{{ $isCurrent ? '⏳' : '✅' }}</div>
                            <div class="flex-1">
                                <h4 class="font-bold text-emerald-800 text-lg mb-2">Status Saat Ini: {{ $applicant->getStatusLabel() }}</h4>
                                <p class="text-sm text-emerald-700">
                                    @if($applicant->status === 'draft')
                                        Formulir Anda sudah tersimpan. Segera lakukan pembayaran untuk melanjutkan.
                                    @elseif($applicant->status === 'submitted')
                                        Pendaftaran Anda sudah diterima. Tim kami akan segera memverifikasi pembayaran Anda.
                                    @elseif($applicant->status === 'payment_verified')
                                        Pembayaran Anda sudah diverifikasi. Silakan upload dokumen yang diperlukan.
                                    @elseif($applicant->status === 'prestasi_verified')
                                        Prestasi Anda sudah diverifikasi. Silakan upload dokumen yang diperlukan.
                                    @elseif($applicant->status === 'document_verified')
                                        Dokumen Anda sudah lengkap. Tunggu jadwal tes masuk.
                                    @elseif($applicant->status === 'tested')
                                        Anda sudah mengikuti tes. Tunggu hasil penilaian.
                                    @elseif($applicant->status === 'scored')
                                        Nilai Anda sudah keluar. Tunggu pengumuman hasil seleksi.
                                    @elseif($applicant->status === 'accepted')
                                        🎉 SELAMAT! Anda DITERIMA! Silakan lakukan daftar ulang.
                                    @elseif($applicant->status === 'reregistered')
                                        Daftar ulang berhasil. Tunggu aktivasi sebagai siswa.
                                    @elseif($applicant->status === 'registered')
                                        🎓 Anda sudah resmi menjadi siswa. Selamat bergabung!
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Documents Section (Only if payment verified) --}}
            @if(in_array($applicant->status, ['payment_verified', 'document_verified', 'tested', 'scored', 'accepted']))
                <div id="dokumen-section" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="text-2xl mr-3">📄</span>
                        Upload Dokumen Pendaftaran
                    </h3>

                    @if(session('document_success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">
                            <p class="font-semibold">✅ {{ session('document_success') }}</p>
                        </div>
                    @endif

                    @if(session('document_error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">
                            <p class="font-semibold">❌ {{ session('document_error') }}</p>
                        </div>
                    @endif

                    {{-- List of Required Documents --}}
                    <div class="bg-blue-50 rounded-xl p-4 mb-6">
                        <p class="font-semibold text-gray-800 mb-3">📋 Dokumen yang Harus Diupload:</p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600">✓</span>
                                <span><strong>Semua Dokumen</strong> (format: PDF/JPG, maks 15MB)</span>
                            </li>
                        </ul>
                        <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <p class="text-xs text-gray-700">📸 <strong>Pas Foto 3x4 (2 lembar)</strong> diserahkan langsung saat daftar ulang</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @php
                        $requiredDocLabels = $applicant->school->getRequiredDocumentLabels();
                        $totalRequired = count($requiredDocLabels) > 0 ? count($requiredDocLabels) : 4;
                        $uploadedCount = $applicant->documents ? $applicant->documents->count() : 0;
                        $progressPercent = ($uploadedCount / $totalRequired) * 100;
                        $allComplete = $uploadedCount >= $totalRequired;
                    @endphp
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Progress Upload Dokumen</span>
                            <span class="text-sm font-bold {{ $allComplete ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $uploadedCount }}/{{ $totalRequired }} Dokumen
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500 {{ $allComplete ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-blue-400 to-indigo-600' }}" 
                                 style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    {{-- Upload Form --}}
                    @if($applicant->status === 'payment_verified' && !$allComplete)
                        @php
                            // Get list of uploaded document types
                            $uploadedTypes = $applicant->documents->pluck('document_type')->toArray();
                            $allDocTypes = $applicant->school->getRequiredDocumentLabels();
                            // If no required docs configured, fall back to defaults
                            if (empty($allDocTypes)) {
                                $allDocTypes = [
                                    'kk' => 'Fotocopy Kartu Keluarga (KK)',
                                    'akta' => 'Fotocopy Akta Kelahiran',
                                    'ijazah' => 'Fotocopy Ijazah/SKHUN',
                                    'raport' => 'Fotocopy Raport (Semester 5 & 6)'
                                ];
                            }
                            $remainingDocs = array_diff_key($allDocTypes, array_flip($uploadedTypes));
                        @endphp

                        @if(count($remainingDocs) > 0)
                            <form id="uploadForm" action="{{ route('public.registration.upload-document') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">
                                <input type="hidden" name="registration_number" value="{{ $applicant->registration_number }}">
                                <input type="hidden" name="nisn" value="{{ $applicant->nisn }}">

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Jenis Dokumen <span class="text-red-500">*</span>
                                    </label>
                                    <select name="document_type" id="documentType" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih Jenis Dokumen --</option>
                                        @foreach($remainingDocs as $type => $label)
                                            <option value="{{ $type }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        File Dokumen <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="document_file" id="documentFile" required accept=".pdf,.jpg,.jpeg,.png" 
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <div id="fileError" class="hidden mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                                        <p class="text-sm text-red-700 font-semibold"></p>
                                    </div>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <span class="text-green-600">✓</span> Format: <strong>PDF, JPG, PNG</strong> saja
                                        </p>
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <span class="text-green-600">✓</span> Ukuran: Maksimal <strong>15MB</strong>
                                        </p>
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <span class="text-green-600">✓</span> File harus <strong>jelas dan terbaca</strong>
                                        </p>
                                    </div>
                                </div>

                                <button type="submit" id="uploadBtn" class="w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-upload mr-2"></i>Upload Dokumen
                                </button>
                            </form>
                        @endif
                    @endif

                    {{-- All Documents Uploaded Message --}}
                    @if($allComplete && $applicant->status === 'payment_verified')
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200">
                            <div class="flex items-start gap-4">
                                <div class="text-5xl animate-bounce">🎉</div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-green-800 text-lg mb-2">Semua Dokumen Sudah Diupload!</h4>
                                    <p class="text-sm text-green-700 mb-3">
                                        Terima kasih! Anda telah menyelesaikan tahap upload dokumen dengan lengkap.
                                    </p>
                                    <div class="bg-white rounded-lg p-4 border border-green-200">
                                        <p class="text-sm font-semibold text-gray-800 mb-2">📌 Langkah Selanjutnya:</p>
                                        <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                            <li>Tim admin akan memverifikasi dokumen Anda (1-2 hari kerja)</li>
                                            <li>Anda akan menerima notifikasi via WhatsApp setelah verifikasi</li>
                                            <li>Setelah dokumen diverifikasi, Anda akan dijadwalkan untuk <strong>Tes Masuk</strong></li>
                                            <li>Informasi jadwal tes akan dikirim via WhatsApp</li>
                                        </ol>
                                    </div>
                                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-xs text-blue-800">
                                            💡 <strong>Tips:</strong> Simpan nomor registrasi Anda (<strong>{{ $applicant->registration_number }}</strong>) 
                                            untuk cek status kapan saja melalui halaman ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- List of Uploaded Documents --}}
                    @if($applicant->documents && $applicant->documents->count() > 0)
                        <div id="uploaded-documents" class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-3">📁 Dokumen yang Sudah Diupload:</h4>
                            <div class="space-y-3">
                                @foreach($applicant->documents as $doc)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border-2 border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">{{ $doc->label }}</p>
                                                <p class="text-xs text-gray-500">{{ $doc->file_name }}</p>
                                                <p class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 2) }} KB</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($doc->verified)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                                    ✓ Verified
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                                    ⏳ Menunggu
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col md:flex-row gap-4 mt-6">
                <a href="{{ route('public.registration.check') }}" class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold text-center hover:shadow-xl transition-all">
                    <i class="fas fa-sync mr-2"></i>Refresh Status
                </a>
                @php
                    $waStatusPhone = str_replace(['+', '-', ' ', '(', ')'], '', $applicant->school->psb_contact_phone ?? '081260932084');
                    if (str_starts_with($waStatusPhone, '0')) {
                        $waStatusPhone = '62' . substr($waStatusPhone, 1);
                    }
                @endphp
                <a href="https://wa.me/{{ $waStatusPhone }}?text=Halo%20Panitia%20PSB%20{{ urlencode($applicant->school->name) }},%20saya%20{{ urlencode($applicant->full_name) }}%20(No.%20Reg:%20{{ $applicant->registration_number }})%20ingin%20bertanya%20terkait%20status%20pendaftaran." 
                   target="_blank"
                   class="flex-1 px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-bold text-center hover:shadow-xl transition-all">
                    <i class="fab fa-whatsapp mr-2"></i>Hubungi Panitia ({{ $applicant->school->psb_contact_person }})
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">© 2026 PembdaHub - Yayasan Pembangunan Daerah</p>
        </div>
    </footer>

    <script>
        // File validation
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('documentFile');
            const uploadBtn = document.getElementById('uploadBtn');
            const fileError = document.getElementById('fileError');
            const uploadForm = document.getElementById('uploadForm');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    // Clear previous errors
                    fileError.classList.add('hidden');
                    uploadBtn.disabled = false;
                    
                    // Check file type
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        showError('❌ Format file tidak valid! Hanya PDF, JPG, dan PNG yang diperbolehkan.');
                        return;
                    }
                    
                    // Check file size (15MB = 15728640 bytes)
                    const maxSize = 15 * 1024 * 1024; // 15MB
                    if (file.size > maxSize) {
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        showError(`❌ Ukuran file terlalu besar (${fileSizeMB} MB)! Maksimal 15MB.`);
                        return;
                    }
                    
                    // Show success message
                    fileError.classList.remove('hidden');
                    fileError.classList.remove('bg-red-50', 'border-red-500');
                    fileError.classList.add('bg-green-50', 'border-green-500');
                    const sizeKB = (file.size / 1024).toFixed(2);
                    fileError.querySelector('p').className = 'text-sm text-green-700 font-semibold';
                    fileError.querySelector('p').textContent = `✓ File valid: ${file.name} (${sizeKB} KB)`;
                });
                
                function showError(message) {
                    fileError.classList.remove('hidden', 'bg-green-50', 'border-green-500');
                    fileError.classList.add('bg-red-50', 'border-red-500');
                    fileError.querySelector('p').className = 'text-sm text-red-700 font-semibold';
                    fileError.querySelector('p').textContent = message;
                    uploadBtn.disabled = true;
                    fileInput.value = '';
                }
            }
            
            // Scroll to documents section if redirected after upload
            @if(session('scroll_to_documents'))
                const dokumenSection = document.getElementById('dokumen-section');
                if (dokumenSection) {
                    setTimeout(() => {
                        dokumenSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            @endif
        });
    </script>
</body>
</html>

