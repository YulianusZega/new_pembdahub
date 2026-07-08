<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">📊 Status Alur Pendaftaran</h3>
    
    @if($applicant->admission_path === 'prestasi')
        <p class="text-xs text-amber-600 font-semibold mb-4">🏆 Jalur Prestasi — Bebas Biaya Pendaftaran</p>
    @else
        <p class="text-xs text-blue-600 font-semibold mb-4">📋 Jalur Reguler</p>
    @endif
    
    @php
        $isPrestasi = $applicant->admission_path === 'prestasi';
        $status = $applicant->status;
        
        // Define completed statuses for each step based on path
        if ($isPrestasi) {
            // Prestasi flow: submitted → prestasi_verified → document_verified → tested/scored → accepted
            $step1Done = in_array($status, ['submitted', 'prestasi_verified', 'document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step2Done = in_array($status, ['prestasi_verified', 'document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step3Done = in_array($status, ['document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step4Done = in_array($status, ['tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step5Done = in_array($status, ['accepted', 'reregistered', 'registered']);
        } else {
            // Reguler flow: submitted → payment_verified → document_verified → tested/scored → accepted
            $step1Done = in_array($status, ['submitted', 'payment_verified', 'document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step2Done = in_array($status, ['payment_verified', 'document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step3Done = in_array($status, ['document_verified', 'tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step4Done = in_array($status, ['tested', 'scored', 'accepted', 'reregistered', 'registered']);
            $step5Done = in_array($status, ['accepted', 'reregistered', 'registered']);
        }
    @endphp

    <div class="flex items-center justify-between">
        <!-- Step 1: Registration -->
        <div class="flex flex-col items-center flex-1">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                {{ $step1Done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                📝
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-700">Pendaftaran</p>
            <p class="text-xs text-gray-500">{{ $applicant->created_at?->format('d M Y') }}</p>
        </div>

        <div class="flex-shrink-0 mx-2">
            <i class="fas fa-arrow-right {{ $step2Done ? 'text-green-500' : 'text-gray-300' }}"></i>
        </div>

        <!-- Step 2: Payment (Reguler) OR Prestasi Verification (Prestasi) -->
        <div class="flex flex-col items-center flex-1">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                {{ $step2Done ? 'bg-green-500 text-white' : ($status === 'submitted' ? 'bg-amber-100 text-amber-500 ring-2 ring-amber-400 animate-pulse' : 'bg-gray-200 text-gray-400') }}">
                {{ $isPrestasi ? '🏆' : '💰' }}
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-700">{{ $isPrestasi ? 'Verifikasi Prestasi' : 'Pembayaran' }}</p>
            <p class="text-xs text-gray-500">
                @if($isPrestasi)
                    {{ $applicant->prestasi_verified_at?->format('d M Y') ?? '-' }}
                @else
                    {{ $applicant->payment_verified_at?->format('d M Y') ?? '-' }}
                @endif
            </p>
        </div>

        <div class="flex-shrink-0 mx-2">
            <i class="fas fa-arrow-right {{ $step3Done ? 'text-green-500' : 'text-gray-300' }}"></i>
        </div>

        <!-- Step 3: Document -->
        <div class="flex flex-col items-center flex-1">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                {{ $step3Done ? 'bg-green-500 text-white' : (in_array($status, ['payment_verified', 'prestasi_verified']) ? 'bg-purple-100 text-purple-500 ring-2 ring-purple-400 animate-pulse' : 'bg-gray-200 text-gray-400') }}">
                📄
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-700">Dokumen</p>
            <p class="text-xs text-gray-500">{{ $applicant->document_verified_at?->format('d M Y') ?? '-' }}</p>
        </div>

        <div class="flex-shrink-0 mx-2">
            <i class="fas fa-arrow-right {{ $step4Done ? 'text-green-500' : 'text-gray-300' }}"></i>
        </div>

        <!-- Step 4: Test -->
        <div class="flex flex-col items-center flex-1">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                {{ $step4Done ? 'bg-green-500 text-white' : ($status === 'document_verified' ? 'bg-pink-100 text-pink-500 ring-2 ring-pink-400 animate-pulse' : 'bg-gray-200 text-gray-400') }}">
                ✍️
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-700">Tes Masuk</p>
            <p class="text-xs text-gray-500">{{ $applicant->test_date?->format('d M Y') ?? '-' }}</p>
        </div>

        <div class="flex-shrink-0 mx-2">
            <i class="fas fa-arrow-right {{ $step5Done ? 'text-green-500' : 'text-gray-300' }}"></i>
        </div>

        <!-- Step 5: Acceptance -->
        <div class="flex flex-col items-center flex-1">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl
                {{ $step5Done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                {{ $status === 'rejected' ? '❌' : '✅' }}
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-700">
                {{ $status === 'rejected' ? 'Ditolak' : 'Diterima' }}
            </p>
            <p class="text-xs text-gray-500">{{ $applicant->accepted_at?->format('d M Y') ?? ($applicant->rejected_at?->format('d M Y') ?? '-') }}</p>
        </div>
    </div>

    {{-- Rejected status bar --}}
    @if($status === 'rejected')
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-center">
            <p class="text-sm font-semibold text-red-700">❌ Pendaftar ditolak {{ $applicant->rejected_at?->format('d M Y H:i') }}</p>
        </div>
    @endif
</div>
