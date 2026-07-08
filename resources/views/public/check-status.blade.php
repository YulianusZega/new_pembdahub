<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pendaftaran - PembdaHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <nav class="bg-white shadow-lg border-b-4 border-blue-500">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                        P
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">PembdaHub</h1>
                        <p class="text-xs text-gray-500">Cek Status Pendaftaran</p>
                    </div>
                </div>
                <a href="{{ route('public.registration.index') }}" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-all text-sm font-semibold">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Baru
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-block w-24 h-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center text-white text-5xl shadow-2xl mb-4 animate-pulse">
                    🔍
                </div>
                <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mb-2">
                    Cek Status Pendaftaran
                </h1>
                <p class="text-gray-600">Masukkan nomor registrasi dan NISN untuk melihat status pendaftaran Anda</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <p class="font-semibold">❌ {{ session('error') }}</p>
                </div>
            @endif

            @if(session('document_success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                    <p class="font-semibold">✅ {{ session('document_success') }}</p>
                </div>
            @endif

            @if(session('document_error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <p class="font-semibold">❌ {{ session('document_error') }}</p>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6">
                <form id="checkStatusForm" action="{{ route('public.registration.check.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            📋 Nomor Registrasi
                        </label>
                        <input type="text" name="registration_number" id="registration_number" required 
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl text-lg font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="SMP-26-0001"
                            value="{{ session('auto_check.registration_number') ?? '' }}">
                        <p class="text-xs text-gray-500 mt-2">
                            Format: XXX-YY-NNNN (contoh: SMP-26-0001)
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            🆔 NISN
                        </label>
                        <input type="text" name="nisn" id="nisn" required 
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl text-lg font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="1234567890"
                            value="{{ session('auto_check.nisn') ?? '' }}">
                        <p class="text-xs text-gray-500 mt-2">
                            10 digit NISN sesuai yang Anda daftarkan
                        </p>
                    </div>

                    <button type="submit" class="w-full px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold text-lg hover:shadow-2xl transition-all hover:scale-105 transform">
                        <i class="fas fa-search mr-2"></i>Cek Status Sekarang
                    </button>
                </form>
            </div>

            {{-- Info Box --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border-2 border-blue-200">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                    <span class="text-xl mr-2">💡</span>
                    Tips:
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500">•</span>
                        <span>Nomor registrasi dikirim via email setelah Anda menyelesaikan pendaftaran</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500">•</span>
                        <span>Pastikan Anda memasukkan NISN yang sama dengan saat pendaftaran</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500">•</span>
                        <span>Jika lupa nomor registrasi, hubungi panitia via WhatsApp di 0812-3456-7890</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">© 2026 PembdaHub - Yayasan Pembangunan Daerah</p>
        </div>
    </footer>

    @if(session('auto_check'))
    <script>
        // Auto-submit form if redirected after document upload
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('checkStatusForm').submit();
        });
    </script>
    @endif
</body>
</html>

