@extends('layouts.admin')
@section('title', 'Kenaikan / Kelulusan Siswa')
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kenaikan & Kelulusan</h1>
                <p class="text-gray-600 mt-1">Proses kenaikan kelas atau kelulusan siswa</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl"><p class="text-green-700">{{ session('success') }}</p></div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl"><p class="text-red-700">{{ session('error') }}</p></div>
    @endif

    <!-- Bulk Promotion Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Proses Massal</h2>
        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas Asal</label>
                    <select name="from_classroom_id" required class="w-full rounded-xl border-gray-300 focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan</label>
                    <select name="decision" required class="w-full rounded-xl border-gray-300 focus:ring-indigo-500">
                        <option value="naik">Naik Kelas</option>
                        <option value="tinggal">Tinggal Kelas</option>
                        <option value="lulus">Lulus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas Tujuan</label>
                    <select name="to_classroom_id" class="w-full rounded-xl border-gray-300 focus:ring-indigo-500">
                        <option value="">-- Jika Naik Kelas --</option>
                        @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->class_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:ring-indigo-500" placeholder="Catatan kenaikan/kelulusan (opsional)"></textarea>
            </div>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-check-double mr-2"></i> Proses Massal
            </button>
        </form>
    </div>

    <!-- Recent Promotions -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Kenaikan/Kelulusan</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm">Siswa</th>
                    <th class="px-6 py-3 text-left text-sm">Dari Kelas</th>
                    <th class="px-6 py-3 text-left text-sm">Ke Kelas</th>
                    <th class="px-6 py-3 text-left text-sm">Keputusan</th>
                    <th class="px-6 py-3 text-left text-sm">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-sm">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($promotions as $promo)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $promo->student->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $promo->fromClassroom->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $promo->toClassroom->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-lg font-semibold @if($promo->decision === 'naik') bg-green-100 text-green-800 @elseif($promo->decision === 'lulus') bg-blue-100 text-blue-800 @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($promo->decision) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $promo->academicYear->year ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $promo->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data kenaikan/kelulusan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $promotions->links() }}</div>
    </div>
</div>
@endsection
