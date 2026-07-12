<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ikatan Alumni (IKA) PEMBDA</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        indigo: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            900: '#312e81',
                        },
                        gold: '#f59e0b',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f4f3ff;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.03) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.03) 0, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 40px -10px rgba(49, 46, 129, 0.1);
            border-radius: 24px;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(49, 46, 129, 0.5);
        }
    </style>
</head>
<body class="text-slate-800 antialiased font-sans">

    <div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
        
        <!-- Header -->
        <div class="text-center mb-8 max-w-3xl mx-auto">
            <div class="flex justify-center items-center gap-3 mb-4">
                <img src="{{ asset('images/logo-pembda.png') }}" alt="Logo PEMBDA" class="h-16 w-auto object-contain">
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-indigo-900 tracking-tight mb-4">
                Pelaporan Data & Rembuk Alumni
            </h1>
            <p class="text-slate-600 text-lg leading-relaxed mb-6">
                Waktu berlalu begitu cepat, tak terasa sudah lebih dari 50 tahun Yayasan Perguruan PEMBDA Nias berdiri. Sudah banyak cerita indah, canda tawa, dan kenangan yang dirajut di ruang-ruang kelas kita. Kini, almamater memanggil Anda kembali. Mari berembuk, berbagi cerita perjalanan hidup Anda, memberikan masukan, serta motivasi bagi adik-adik dan almamater tercinta.
            </p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 px-6 py-2.5 rounded-full text-sm font-bold shadow-md hover:shadow-lg transition-all border border-indigo-100 hover:bg-indigo-50 hover:-translate-y-0.5">
                <i class="fa-solid fa-right-to-bracket"></i> Sudah Punya Akun? Login di Sini
            </a>
        </div>

        <!-- Smart Report -->
        <div class="max-w-3xl w-full mx-auto mb-10 p-6 bg-gradient-to-r from-indigo-50 to-white border border-indigo-100 rounded-2xl shadow-sm text-center">
            <h3 class="text-sm font-bold text-indigo-800 uppercase tracking-wider mb-4"><i class="fas fa-chart-pie mr-2 text-indigo-500"></i> Report Pintar Alumni</h3>
            <div class="flex flex-wrap justify-center gap-4 md:gap-8">
                <div class="bg-white px-6 py-4 rounded-xl shadow-sm border border-slate-100 flex-1 min-w-[120px]">
                    <span class="block text-xs font-semibold text-slate-500 mb-1">Total Pendaftar</span>
                    <strong class="text-3xl font-extrabold text-indigo-600">{{ $totalRegistered ?? 0 }}</strong>
                </div>
                <div class="bg-white px-6 py-4 rounded-xl shadow-sm border border-slate-100 flex-1 min-w-[120px]">
                    <span class="block text-xs font-semibold text-slate-500 mb-1">Angkatan Tertua</span>
                    <strong class="text-3xl font-extrabold text-gold">{{ $oldestAlumni ?? '-' }}</strong>
                </div>
                <div class="bg-white px-6 py-4 rounded-xl shadow-sm border border-slate-100 flex-1 min-w-[120px]">
                    <span class="block text-xs font-semibold text-slate-500 mb-1">Angkatan Termuda</span>
                    <strong class="text-3xl font-extrabold text-emerald-500">{{ $youngestAlumni ?? '-' }}</strong>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4 font-medium">Jadilah bagian dari sejarah panjang PEMBDA dengan mendaftarkan diri Anda sekarang!</p>
        </div>

        <!-- Info & Guide Section -->
        <div class="max-w-5xl w-full mx-auto mb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info 1: Tujuan -->
                <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl border border-indigo-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 mb-4">
                        <i class="fa-solid fa-bullseye text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-indigo-900 mb-2">Mengapa Harus Mendaftar?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kami ingin mendata ulang dan menyatukan seluruh keluarga besar alumni dari berbagai generasi. Platform ini dibangun khusus agar Anda bisa terus memantau dan berkontribusi untuk perkembangan yayasan tercinta kita.
                    </p>
                </div>
                <!-- Info 2: Keuntungan -->
                <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl border border-gold/20 shadow-sm hover:shadow-md transition relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-gold/10 rounded-full blur-xl"></div>
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-gold mb-4 relative z-10">
                        <i class="fa-solid fa-gift text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-indigo-900 mb-2 relative z-10">Fasilitas Eksklusif</h3>
                    <p class="text-sm text-slate-600 leading-relaxed relative z-10">
                        Setelah mendaftar, Anda akan mendapatkan <strong>Akun Portal Alumni</strong> secara otomatis! Dengan akun ini, Anda bisa berjejaring di forum <em>Pembda Space</em>, mengisi Tracer Study, dan melihat lowongan karir khusus alumni.
                    </p>
                </div>
                <!-- Info 3: Cara Kerja -->
                <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl border border-emerald-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500 mb-4">
                        <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-indigo-900 mb-2">Bagaimana Caranya?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sangat mudah! Cukup <strong>isi formulir pelaporan data di bawah</strong> halaman ini. Saat berhasil dikirim, sistem akan langsung memberikan <em>Username</em> dan <em>Password</em> untuk Anda *Login* ke Portal Alumni.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        @if(isset($approvedAlumni) && $approvedAlumni->count() > 0)
        <div class="w-full max-w-6xl mb-12">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-indigo-900 inline-block relative">
                    Keluarga Besar yang Telah Berembuk
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-gold rounded-full"></div>
                </h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($approvedAlumni as $alumni)
                <div class="glass-card overflow-hidden hover:transform hover:scale-105 transition duration-300 group cursor-pointer relative" style="border-radius: 16px;">
                    <!-- Container dengan aspect ratio 3:4 (133% padding-top) -->
                    <div class="w-full relative" style="padding-top: 133.33%;">
                        <img src="{{ $alumni->photo_url }}" class="absolute inset-0 w-full h-full object-cover object-top" alt="{{ $alumni->full_name }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                            <h4 class="font-bold text-sm leading-tight line-clamp-2" title="{{ $alumni->full_name }}">{{ $alumni->alias_name ? $alumni->alias_name : $alumni->full_name }}</h4>
                            <p class="text-[10px] text-gray-300 mt-1 line-clamp-1"><i class="fas fa-graduation-cap text-gold mr-1"></i>{{ $alumni->school->name ?? 'PEMBDA' }} '{{ $alumni->graduation_year }}</p>
                            @if($alumni->occupation)
                            <p class="text-[10px] text-indigo-200 mt-0.5 line-clamp-1"><i class="fas fa-briefcase mr-1"></i>{{ $alumni->occupation }}</p>
                            @endif
                        </div>
                    </div>
                    @if($alumni->message)
                    <div class="absolute inset-0 bg-indigo-900/95 p-4 text-white opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center text-center">
                        <i class="fas fa-quote-left text-indigo-400 text-xl mb-2"></i>
                        <p class="text-xs italic line-clamp-5">{{ $alumni->message }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="w-full max-w-4xl glass-card p-6 md:p-10">
            
            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex gap-3 items-start">
                    <i class="fa-solid fa-circle-check text-xl mt-0.5 text-emerald-500"></i>
                    <div>
                        <h4 class="font-bold text-emerald-900">Pelaporan Data Berhasil!</h4>
                        <p class="text-sm mt-1">{!! session('success') !!}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex gap-3 items-start">
                    <i class="fa-solid fa-triangle-exclamation text-xl mt-0.5 text-red-500"></i>
                    <div>
                        <h4 class="font-bold text-red-900">Mohon periksa kembali isian Anda:</h4>
                        <ul class="text-sm mt-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('ika.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Section: Data Pribadi -->
                <div>
                    <h3 class="text-xl font-bold text-indigo-900 mb-4 pb-2 border-b border-indigo-100 flex items-center gap-2">
                        <i class="fa-regular fa-id-badge text-indigo-500"></i> 1. Identitas Pribadi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap (beserta Gelar) <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required class="form-input" placeholder="Cth: Dr. Budi Santoso, M.Kom">
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Panggilan / Nama Keluarga (Nias: Ama... / Ina...)</label>
                            <input type="text" name="alias_name" value="{{ old('alias_name') }}" class="form-input" placeholder="Cth: Ama Budi / Ina Wati">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" required class="form-input">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor WhatsApp / HP</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="Cth: 08123456789">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pernikahan</label>
                            <select name="marital_status" class="form-input">
                                <option value="">-- Pilih Status --</option>
                                <option value="Belum Menikah" {{ old('marital_status') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                <option value="Menikah" {{ old('marital_status') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="Pernah Menikah" {{ old('marital_status') == 'Pernah Menikah' ? 'selected' : '' }}>Pernah Menikah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah Anak (Jika Ada)</label>
                            <input type="number" name="children_count" value="{{ old('children_count') }}" min="0" class="form-input" placeholder="0">
                        </div>

                        <div class="col-span-1 md:col-span-2 border-t border-slate-100 pt-4 mt-2">
                            <h4 class="text-sm font-bold text-indigo-800 mb-4">Informasi Pekerjaan</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Profesi / Pekerjaan Saat Ini</label>
                                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="form-input" placeholder="Cth: Pengusaha / PNS / Wiraswasta">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Perusahaan / Instansi</label>
                                    <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-input" placeholder="Cth: PT Maju Jaya / Pemda Nias">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 border-t border-slate-100 pt-4 mt-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email Aktif</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="Cth: budi@email.com">
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Domisili <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="2" required class="form-input" placeholder="Tuliskan alamat lengkap domisili Anda saat ini...">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Akademik -->
                <div>
                    <h3 class="text-xl font-bold text-indigo-900 mb-4 pb-2 border-b border-indigo-100 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-indigo-500"></i> 2. Rekam Akademik di PEMBDA
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alumni Unit Sekolah <span class="text-red-500">*</span></label>
                            <select id="school-select" name="school_id" required class="form-input">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" data-type="{{ $school->type }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun Lulus <span class="text-red-500">*</span></label>
                            <select name="graduation_year" required class="form-input">
                                <option value="">-- Pilih Tahun Lulus --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ old('graduation_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas Terakhir (opsional)</label>
                            <input type="text" name="last_class" value="{{ old('last_class') }}" class="form-input" placeholder="Cth: XII IPA 1 / 3 Sos 2">
                        </div>
                        
                        <div id="jurusan-container" style="display: none;">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan (Khusus SMK) <span class="text-red-500">*</span></label>
                            <input type="text" id="jurusan-input" name="jurusan" value="{{ old('jurusan') }}" class="form-input" placeholder="Cth: Akuntansi / TKJ / Perkantoran">
                        </div>
                    </div>
                </div>

                <!-- Section: Pesan & Foto -->
                <div>
                    <h3 class="text-xl font-bold text-indigo-900 mb-4 pb-2 border-b border-indigo-100 flex items-center gap-2">
                        <i class="fa-regular fa-image text-indigo-500"></i> 3. Pesan & Foto Profil
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pesan, Kesan, atau Ide untuk Almamater</label>
                            <textarea name="message" rows="4" class="form-input" placeholder="Tuliskan pesan-pesan indah Anda semasa sekolah, atau ide untuk memajukan Yayasan PEMBDA Nias...">{{ old('message') }}</textarea>
                            <p class="text-xs text-slate-400 mt-1">Pesan ini akan menjadi inspirasi bagi adik-adik kelas yang masih belajar.</p>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Unggah Foto Profil Terbaru (opsional)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl bg-slate-50 hover:bg-indigo-50 transition">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-400 mb-2"></i>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2 py-0.5 shadow-sm border border-slate-200">
                                            <span>Pilih File Foto</span>
                                            <input id="photo" name="photo" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2">PNG, JPG, JPEG (Maks. 4MB)</p>
                                </div>
                            </div>
                            
                            <!-- Image Preview Area -->
                            <div id="image-preview-container" class="mt-4 hidden justify-center">
                                <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex justify-between items-center border-t border-slate-200">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
                    </a>
                    <button type="submit" class="btn-primary py-3 px-8 rounded-xl font-bold text-lg inline-flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pelaporan Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center text-sm text-slate-500">
            <p>&copy; {{ date('Y') }} Yayasan Perguruan PEMBDA Nias.</p>
            <p>Powered by <strong class="text-indigo-900">PembdaHUB</strong></p>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('flex');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
                previewImage.src = "#";
            }
        }
        function toggleJurusan() {
            const schoolSelect = document.getElementById('school-select');
            if(!schoolSelect) return;
            
            const selectedOption = schoolSelect.options[schoolSelect.selectedIndex];
            const type = selectedOption ? selectedOption.getAttribute('data-type') : null;
            
            const jurusanContainer = document.getElementById('jurusan-container');
            const jurusanInput = document.getElementById('jurusan-input');
            
            if (type && type.toUpperCase().includes('SMK')) {
                jurusanContainer.style.display = 'block';
                jurusanInput.setAttribute('required', 'required');
            } else {
                jurusanContainer.style.display = 'none';
                jurusanInput.removeAttribute('required');
                // jurusanInput.value = ''; // Opsional: jangan reset value kalau user salah pencet
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('school-select');
            if(schoolSelect) {
                schoolSelect.addEventListener('change', toggleJurusan);
                toggleJurusan(); // run once on load
            }
        });
    </script>
</body>
</html>
