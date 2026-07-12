<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PembdaHUB</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        {{-- Error Icon --}}
        <div class="mb-6">
            @yield('icon')
        </div>

        {{-- Error Code --}}
        <h1 class="text-8xl font-extrabold text-transparent bg-clip-text @yield('gradient', 'bg-gradient-to-r from-indigo-500 to-purple-600') mb-4">
            @yield('code')
        </h1>

        {{-- Error Title --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-3">
            @yield('heading')
        </h2>

        {{-- Error Message --}}
        <p class="text-gray-500 mb-8 leading-relaxed">
            @yield('description')
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @hasSection('actions')
                @yield('actions')
            @else
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Ke Beranda
                </a>
                <button onclick="history.back()"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors border border-gray-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </button>
            @endif
        </div>

        {{-- Footer --}}
        <p class="mt-12 text-xs text-gray-400">
            &copy; {{ date('Y') }} PembdaHUB — Sistem Manajemen Sekolah Terpadu
        </p>
    </div>
</body>
</html>
