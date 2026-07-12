@extends('layouts.admin')
@section('title', 'Detail Alumni - ' . $directory->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.alumni-directory.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Direktori
        </a>
        <div class="flex gap-2">
            <form action="{{ route('admin.alumni-directory.toggle-approval', $directory) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white {{ $directory->is_approved ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }} shadow-sm transition">
                    <i class="fas {{ $directory->is_approved ? 'fa-times' : 'fa-check' }} mr-1"></i>
                    {{ $directory->is_approved ? 'Batalkan Persetujuan' : 'Setujui Tampil Publik' }}
                </button>
            </form>
            <form action="{{ route('admin.alumni-directory.destroy', $directory) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-red-500 hover:bg-red-600 shadow-sm transition">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-8 sm:p-10 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white flex flex-col sm:flex-row gap-8 items-start">
            <img src="{{ $directory->photo_url }}" class="w-32 h-32 rounded-2xl object-cover shadow-md border-4 border-white" alt="Foto Profil">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $directory->full_name }}</h1>
                <div class="mt-2 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium">
                        <i class="fas fa-graduation-cap"></i> {{ $directory->school->name ?? 'Tidak diketahui' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium">
                        <i class="fas fa-calendar-alt"></i> Lulus Tahun {{ $directory->graduation_year }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $directory->is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-sm font-medium">
                        <i class="fas {{ $directory->is_approved ? 'fa-check-circle' : 'fa-clock' }}"></i> 
                        {{ $directory->is_approved ? 'Disetujui Publik' : 'Menunggu Persetujuan' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8 sm:p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 border-gray-100">Informasi Pribadi & Kontak</h3>
                
                <div class="grid grid-cols-2 gap-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">{{ $directory->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pekerjaan</p>
                        <p class="font-medium text-gray-900">{{ $directory->occupation ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No. WhatsApp/HP</p>
                        <p class="font-medium text-gray-900">{{ $directory->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium text-gray-900">{{ $directory->email ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Alamat Lengkap</p>
                        <p class="font-medium text-gray-900 mt-1">{{ $directory->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 border-gray-100">Pesan & Kesan</h3>
                
                <div>
                    <p class="text-sm text-gray-500 mb-2">Pesan, Kesan, atau Ide untuk Almamater:</p>
                    @if($directory->message)
                        <div class="p-5 bg-gray-50 rounded-xl text-gray-700 italic border border-gray-100 relative">
                            <i class="fas fa-quote-left absolute top-4 left-4 text-gray-200 text-2xl"></i>
                            <p class="relative z-10 pl-6 leading-relaxed">{{ $directory->message }}</p>
                        </div>
                    @else
                        <p class="text-gray-400 italic">Tidak ada pesan yang disampaikan.</p>
                    @endif
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Kelas Terakhir di PEMBDA:</p>
                    <p class="font-medium text-gray-900">{{ $directory->last_class ?? 'Tidak menyebutkan' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
