<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Pembda Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-indigo-600 mb-2">Pembda Hub</h1>
                <p class="text-gray-600">Sistem Manajemen Sekolah Terpadu</p>
            </div>

            <!-- Forgot Password Form Card -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Lupa Password?</h2>
                <p class="text-gray-600 text-sm mb-6">
                    Masukkan email Anda dan kami akan mengirimkan link reset password.
                </p>

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-600 text-sm font-semibold mb-2">Terjadi kesalahan:</p>
                    <ul class="text-red-600 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-600 text-sm">{{ session('status') }}</p>
                </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                            placeholder="your@email.com">
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-200">
                        Kirim Link Reset
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center text-sm">
                    <p class="text-gray-600">
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700">Kembali ke login</a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm">
                <p>&copy; 2024 Pembda Hub. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</body>

</html>