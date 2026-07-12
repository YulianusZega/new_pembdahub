<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sekolah - PembdaHUB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold">PembdaHUB - Admin Sekolah</div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="hover:bg-blue-700 px-3 py-2 rounded">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Admin Sekolah</h1>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm font-semibold mb-2">Total Siswa</div>
                <div class="text-3xl font-bold text-blue-600">285</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm font-semibold mb-2">Total Guru</div>
                <div class="text-3xl font-bold text-green-600">35</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm font-semibold mb-2">Total Kelas</div>
                <div class="text-3xl font-bold text-purple-600">9</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm font-semibold mb-2">Tagihan Tertunggak</div>
                <div class="text-3xl font-bold text-red-600">Rp 45 Juta</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    Kelola Siswa
                </button>
                <button class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition">
                    Kelola Guru
                </button>
                <button class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition">
                    Kelola Kelas
                </button>
                <button class="bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    Kelola Tagihan
                </button>
            </div>
        </div>
    </div>
</body>

</html>