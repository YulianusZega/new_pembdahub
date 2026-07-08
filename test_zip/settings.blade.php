@extends(auth()->user()->layout)

@section('title', 'Pengaturan Profil & Keamanan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto py-4">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-850 flex items-center gap-2">
            <i class="fas fa-user-shield text-indigo-500"></i> Pengaturan Profil & Keamanan
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Perbarui informasi akun dan keamanan kata sandi Anda</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl flex items-center gap-3 shadow-sm transition-all duration-300">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="font-semibold text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="font-bold text-sm">Terjadi Kesalahan Validasi</p>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card Info Ringkas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between h-fit">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-center text-white">
                <div class="w-20 h-20 mx-auto bg-white/20 rounded-2xl flex items-center justify-center text-4xl mb-3 shadow-inner">
                    <img src="{{ $user->photo_url }}" class="w-full h-full object-cover rounded-2xl" alt="Foto Profil">
                </div>
                <h2 class="text-lg font-bold truncate">{{ $user->name }}</h2>
                <p class="text-white/80 text-xs mt-1 bg-white/10 inline-block px-3 py-1 rounded-full uppercase tracking-wider font-semibold">
                    {{ str_replace('_', ' ', $user->role) }}
                </p>
            </div>
            <div class="p-5 space-y-3 bg-slate-50/50">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-medium">Status Akun</span>
                    <span class="px-2 py-0.5 rounded-md font-bold bg-green-100 text-green-700">Aktif</span>
                </div>
                @if($user->school)
                    <div class="flex justify-between items-start text-xs gap-4">
                        <span class="text-gray-400 font-medium flex-shrink-0">Sekolah</span>
                        <span class="text-gray-700 font-semibold text-right">{{ $user->school->name }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-medium">Username</span>
                    <span class="text-gray-700 font-mono font-semibold">{{ $user->username }}</span>
                </div>
            </div>
        </div>

        {{-- Form Edit --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('profile.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Section Informasi Akun --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fas fa-id-card text-indigo-500"></i> Informasi Kredensial
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Nama Pengguna (Username)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-user-tag text-xs"></i>
                                </span>
                                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" 
                                    class="pl-9 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Alamat Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-envelope text-xs"></i>
                                </span>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                    class="pl-9 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Keamanan (Password) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fas fa-key text-indigo-500"></i> Keamanan & Kata Sandi
                    </h3>
                    <p class="text-xs text-gray-400 italic">Biarkan kosong jika Anda tidak ingin mengubah kata sandi.</p>

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Kata Sandi Saat Ini</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="current_password" id="current_password" 
                                    class="pl-9 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Kata Sandi Baru</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-lock-open text-xs"></i>
                                    </span>
                                    <input type="password" name="password" id="password" 
                                        class="pl-9 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Min. 8 karakter">
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Konfirmasi Kata Sandi Baru</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-shield text-xs"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="pl-9 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Ulangi kata sandi baru">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex justify-end gap-3">
                    <button type="submit" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 shadow-sm flex items-center gap-2 hover:-translate-y-0.5">
                        <i class="fas fa-save text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Form Biodata Diri (Self-Service) --}}
        @if($biodata)
        <div class="lg:col-span-2 lg:col-start-2 space-y-6">
            <form action="{{ route('profile.biodata.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-address-card text-indigo-500"></i> Biodata Lengkap
                        </h3>
                        @if(!$isBiodataEditable)
                            <span class="bg-rose-100 text-rose-700 text-[10px] uppercase font-bold px-2 py-1 rounded-md">Periode Berakhir</span>
                        @else
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] uppercase font-bold px-2 py-1 rounded-md">Periode Aktif (Juli 2026)</span>
                        @endif
                    </div>
                    
                    @if(!$isBiodataEditable)
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded-lg text-xs text-amber-800 mb-4">
                        <i class="fas fa-info-circle mr-1"></i> Waktu pembaruan biodata mandiri telah berakhir pada Juli 2026. Anda tidak dapat mengubah data ini lagi.
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Foto Profil</label>
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                                @if(!$isBiodataEditable) disabled @endif
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 disabled:opacity-50">
                            <p class="text-[10px] text-gray-400 mt-1">Maks 2MB (JPG/PNG). Biarkan kosong jika tidak ingin mengubah.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Tempat Lahir</label>
                            <input type="text" name="birth_place" value="{{ old('birth_place', $biodata->birth_place) }}"
                                @if(!$isBiodataEditable) disabled @endif
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-50 disabled:text-gray-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', optional($biodata->birth_date)->format('Y-m-d')) }}"
                                @if(!$isBiodataEditable) disabled @endif
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-50 disabled:text-gray-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Agama</label>
                            <select name="religion" @if(!$isBiodataEditable) disabled @endif class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('religion', $biodata->religion) === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Nomor Telepon/HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $biodata->phone) }}"
                                @if(!$isBiodataEditable) disabled @endif
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-50 disabled:text-gray-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Alamat Lengkap</label>
                            <textarea name="address" rows="3" @if(!$isBiodataEditable) disabled @endif
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-50 disabled:text-gray-500">{{ old('address', $biodata->address) }}</textarea>
                        </div>
                    </div>
                </div>

                @if($isBiodataEditable)
                <div class="flex justify-end gap-3">
                    <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 shadow-sm flex items-center gap-2 hover:-translate-y-0.5">
                        <i class="fas fa-save text-xs"></i> Simpan Biodata
                    </button>
                </div>
                @endif
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
