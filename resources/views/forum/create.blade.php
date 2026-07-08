@extends(auth()->user()->layout)

@section('title', 'Bikin Postingan Baru 🚀')

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&display=swap" rel="stylesheet">
<style>
    .forum-hdr {
        font-family: 'Space Grotesk', sans-serif;
    }
</style>

<div class="forum-custom-workspace max-w-full w-full px-4 sm:px-8 xl:px-12 py-8 space-y-6" x-data="{ 
    category: 'diskusi',
    perfType: 'badge',
    hasTargetVolunteers: false,
    hasTargetDonation: false,
    imageUrl: null,
    fileChosen(event) {
        const file = event.target.files[0];
        if (file) {
            this.imageUrl = URL.createObjectURL(file);
        } else {
            this.imageUrl = null;
        }
    }
}">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-base font-black text-slate-650 hover:text-indigo-600 transition">
            <i class="ph-bold ph-chevron-left"></i> Kembali ke Beranda Forum 🔙
        </a>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-xl overflow-hidden">
        <!-- Card Header -->
        <div class="px-10 py-10 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <h3 class="forum-hdr text-3xl md:text-4xl font-black tracking-tight">Ekspresikan Dirimu! ⚡</h3>
            <p class="text-sm md:text-base text-indigo-50 mt-1.5 font-bold">Bagikan karya gokil, ajak kolaborasi mabar/proyek, dan kumpulkan reputasi Elite Poin!</p>
        </div>

        <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
            @csrf

            <!-- Category Grid Selection -->
            <div class="space-y-4">
                <label class="block text-sm font-black text-slate-655 uppercase tracking-widest px-1">Pilih Kategori Obrolan / Karya <span class="text-rose-500">*</span></label>
                
                <input type="hidden" name="category" :value="category">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    <!-- Diskusi -->
                    <div @click="category = 'diskusi'" 
                         :class="category === 'diskusi' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20 shadow-md shadow-indigo-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-chat-circle-dots"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">💬 Tanya & Bahas</h5>
                            <p class="text-xs text-slate-550 mt-1 font-bold leading-normal">Tanyakan PR atau bahas topik pelajaran bareng.</p>
                        </div>
                    </div>

                    <!-- Sharing -->
                    <div @click="category = 'sharing'" 
                         :class="category === 'sharing' ? 'border-emerald-600 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-md shadow-emerald-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-file-lines"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">📂 Bagi File & Catatan</h5>
                            <p class="text-xs text-slate-550 mt-1 font-bold leading-normal">Bagi file rangkuman materi, PDF, atau presentasimu.</p>
                        </div>
                    </div>

                    <!-- Info (Restricted) -->
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah() || auth()->user()->isGuru())
                    <div @click="category = 'info'" 
                         :class="category === 'info' ? 'border-amber-600 bg-amber-50/40 ring-2 ring-amber-500/20 shadow-md shadow-amber-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-megaphone"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">📢 Info & Wara-Wara</h5>
                            <p class="text-xs text-slate-550 mt-1 font-bold leading-normal">Info pengumuman resmi sekolah/yayasan Pembda.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Panggung Eksistensi -->
                    <div @click="category = 'performance'" 
                         :class="category === 'performance' ? 'border-purple-650 bg-purple-50/40 ring-2 ring-purple-500/20 shadow-md shadow-purple-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-trophy"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">🏆 Panggung Eksistensi</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Pamerkan karyamu, prestasi non-akademik, atau karya seni.</p>
                        </div>
                    </div>

                    <!-- Galeri Seni & Foto -->
                    <div @click="category = 'art_gallery'" 
                         :class="category === 'art_gallery' ? 'border-pink-600 bg-pink-50/40 ring-2 ring-pink-500/20 shadow-md shadow-pink-650/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-pink-100 flex items-center justify-center text-pink-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-palette"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">🎨 Galeri Seni & Foto</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Post gambar digital, foto estetik, ilustrasi, atau desain.</p>
                        </div>
                    </div>

                    <!-- Unjuk Bakat & Musik -->
                    <div @click="category = 'talent'" 
                         :class="category === 'talent' ? 'border-violet-600 bg-violet-50/40 ring-2 ring-violet-500/20 shadow-md shadow-violet-650/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-music-notes"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">🎵 Unjuk Bakat & Musik</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Share video cover lagu, dance, band, or main musik.</p>
                        </div>
                    </div>

                    <!-- E-Sports & Mabar -->
                    <div @click="category = 'gaming'" 
                         :class="category === 'gaming' ? 'border-rose-600 bg-rose-50/40 ring-2 ring-rose-500/20 shadow-md shadow-rose-655/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-game-controller"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">🎮 E-Sports & Mabar</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Cari tim/teman mabar, diskusikan game, anime, & hobi.</p>
                        </div>
                    </div>

                    <!-- Portofolio Juara -->
                    <div @click="category = 'portfolio'" 
                         :class="category === 'portfolio' ? 'border-cyan-600 bg-cyan-50/40 ring-2 ring-cyan-500/20 shadow-md shadow-cyan-655/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-cyan-100 flex items-center justify-center text-cyan-650 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-certificate"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">🚀 Portofolio Juara</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Pamerkan sertifikat, piala, lomba eksternal sekolah.</p>
                        </div>
                    </div>

                    <!-- Project Idea -->
                    <div @click="category = 'project_idea'" 
                         :class="category === 'project_idea' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-500/20 shadow-md shadow-blue-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-lightbulb"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">💡 Kolab Ide & Project</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Rekrut kru/tim buat bikin aplikasi, inovasi, atau startup.</p>
                        </div>
                    </div>

                    <!-- Committee -->
                    <div @click="category = 'committee'" 
                         :class="category === 'committee' ? 'border-teal-600 bg-teal-50/40 ring-2 ring-teal-500/20 shadow-md shadow-teal-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-users-three"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">👥 Rekrut Panitia</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Cari panitia kegiatan OSIS, class meeting, atau pensi.</p>
                        </div>
                    </div>

                    <!-- Charity -->
                    <div @click="category = 'charity'" 
                         :class="category === 'charity' ? 'border-rose-600 bg-rose-50/40 ring-2 ring-rose-500/20 shadow-md shadow-rose-600/5' : 'border-slate-150 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-350'" 
                         class="p-5 border-2 rounded-2xl cursor-pointer transition flex gap-3.5 items-start">
                        <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 text-lg">
                            <i class="ph-bold ph-heart"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 block">❤️ Aksi Sosial & Donasi</h5>
                            <p class="text-xs text-slate-555 mt-1 font-bold leading-normal">Galang dana kemanusiaan atau kumpulin relawan sosial.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Judul -->
            <div class="space-y-2">
                <label class="block text-sm font-black text-slate-655 uppercase tracking-widest px-1">Judul Postingan / Judul Karya Kece Kamu <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       class="w-full px-5 py-4 border-2 border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-base font-bold text-slate-850 transition outline-none" 
                       placeholder="Bikin judul yang menarik biar rame yang lihat..." required>
            </div>

            <!-- Konten -->
            <div class="space-y-2">
                <label class="block text-sm font-black text-slate-655 uppercase tracking-widest px-1">Tulis Isi Postinganmu Di Sini <span class="text-rose-500">*</span></label>
                <textarea name="content" rows="8" 
                          class="w-full px-5 py-5 border-2 border-slate-200 hover:border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl text-base font-bold text-slate-850 transition outline-none resize-y" 
                          placeholder="Jelaskan detail karya kece, ide mabar, atau pertanyaan tugas sekolahmu..." required>{{ old('content') }}</textarea>
            </div>

            <!-- --- DYNAMIC SECTION: PERFORMANCE/ACHIEVEMENT SHOWCASE --- -->
            <div x-show="['performance', 'art_gallery', 'talent', 'portfolio'].includes(category)" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="p-6 rounded-2xl bg-indigo-50/60 border border-indigo-150 space-y-4">
                <h4 class="text-base font-black text-indigo-900"><i class="ph-bold ph-medal mr-1.5 text-amber-500"></i> 🏅 Hubungkan dengan Prestasi Karaktermu (Biar Tambah Keren!)</h4>
                <p class="text-sm text-indigo-900/90 leading-relaxed font-bold">
                    Pilih lencana kece atau nilai CBT gokilmu biar nampang langsung di postingan ini sebagai bukti prestasi otentikmu!
                </p>
                
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-sm font-black text-slate-700 select-none">
                        <input type="radio" name="reference_type" value="badge" x-model="perfType" checked class="text-indigo-600 focus:ring-indigo-500">
                        <span>🎖️ Lencana Terkunci</span>
                    </label>
                    @if(auth()->user()->isSiswa())
                    <label class="flex items-center gap-2 cursor-pointer text-sm font-black text-slate-700 select-none">
                        <input type="radio" name="reference_type" value="grade" x-model="perfType" class="text-indigo-600 focus:ring-indigo-500">
                        <span>💯 Nilai Ujian CBT</span>
                    </label>
                    @endif
                </div>

                <!-- Badges Selector -->
                <div x-show="perfType === 'badge'" class="space-y-1.5">
                    <select name="reference_id" 
                            class="w-full px-5 py-4 bg-white border-2 border-slate-200 focus:border-indigo-400 transition outline-none cursor-pointer rounded-xl text-sm font-bold text-slate-755">
                        <option value="">-- Pilih Lencana Terhebatmu --</option>
                        @foreach($badges as $badge)
                            <option value="{{ $badge->id }}">{{ $badge->name }} (Poin: {{ $badge->requirement_value }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- CBT Results Selector -->
                @if(auth()->user()->isSiswa())
                <div x-show="perfType === 'grade'" class="space-y-1.5">
                    <select name="reference_id" 
                            class="w-full px-5 py-4 bg-white border-2 border-slate-200 focus:border-indigo-400 transition outline-none cursor-pointer rounded-xl text-sm font-bold text-slate-755">
                        <option value="">-- Pilih Nilai CBT Tergokil Anda --</option>
                        @foreach($cbtResults as $result)
                            <option value="{{ $result->id }}">{{ $result->exam->exam_title }} - Nilai: {{ $result->final_score }} (KKM: {{ $result->exam->passing_score }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <!-- --- DYNAMIC SECTION: COLLABORATION RECRUITMENT --- -->
            <div x-show="category === 'project_idea' || category === 'committee'" 
                 x-transition:enter="transition ease-out duration-300"
                 class="p-6 rounded-2xl bg-emerald-50 border border-emerald-250 space-y-4">
                <h4 class="text-base font-black text-emerald-900"><i class="ph-bold ph-user-group mr-1.5 text-emerald-600"></i> 🤝 Cari Anggota Tim / Rekrutmen Panitia</h4>
                <p class="text-sm text-emerald-800 leading-relaxed font-bold">
                    Biar temen-temen yang lain bisa nge-klik tombol "Gabung Tim" buat daftar kolaborasi bareng kamu. Kamu bisa menyetujui/menolak mereka di halaman detail postingan.
                </p>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="recruitment_enabled" value="1" checked id="recruitCheck" 
                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                    <label for="recruitCheck" class="text-sm font-black text-slate-755 cursor-pointer select-none">Aktifkan pendaftaran kru/anggota baru</label>
                </div>
            </div>

            <!-- --- DYNAMIC SECTION: CHARITY & VOLUNTEERING --- -->
            <div x-show="category === 'charity'" 
                 x-transition:enter="transition ease-out duration-300"
                 class="p-6 rounded-2xl bg-amber-50 border border-amber-250 space-y-6">
                <h4 class="text-base font-black text-amber-900"><i class="ph-bold ph-heart mr-1.5 text-amber-600"></i> ❤️ Info Penggalangan Dana & Sukarelawan</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Donation Target -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="hasTargetDonation" id="donationCheck" class="w-4 h-4 text-amber-600 rounded cursor-pointer">
                            <label for="donationCheck" class="text-sm font-black text-slate-755 uppercase tracking-wider cursor-pointer select-none">Ada Target Donasi Uang</label>
                        </div>
                        <div x-show="hasTargetDonation" class="space-y-1.5" x-transition>
                            <label class="text-[11px] font-black text-slate-550 uppercase tracking-widest block">Target Nominal Uang (Rp)</label>
                            <input type="number" name="charity_target_amount" class="w-full px-5 py-3 border-2 border-slate-200 focus:border-indigo-500 rounded-xl text-base font-bold text-slate-800" placeholder="Contoh: 5000000">
                        </div>
                    </div>

                    <!-- Volunteer Target -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="hasTargetVolunteers" id="volunteerCheck" class="w-4 h-4 text-amber-600 rounded cursor-pointer">
                            <label for="volunteerCheck" class="text-sm font-black text-slate-755 uppercase tracking-wider cursor-pointer select-none">Butuh Relawan / Volunteer Kegiatan</label>
                        </div>
                        <div x-show="hasTargetVolunteers" class="space-y-1.5" x-transition>
                            <label class="text-[11px] font-black text-slate-550 uppercase tracking-widest block">Target Jumlah Relawan (Orang)</label>
                            <input type="number" name="charity_target_volunteers" class="w-full px-5 py-3 border-2 border-slate-200 focus:border-indigo-500 rounded-xl text-base font-bold text-slate-800" placeholder="Contoh: 15">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload File & Gambar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-150">
                <!-- Cover Image Upload & Preview -->
                <div class="space-y-3">
                    <label class="block text-sm font-black text-slate-655 uppercase tracking-widest px-1">🖼️ Unggah Foto Utama / Cover Karya Kece (Opsional)</label>
                    <input type="file" name="image" accept="image/*" @change="fileChosen" 
                           class="w-full px-4 py-3 border-2 border-dashed border-slate-300 hover:border-indigo-300 rounded-2xl text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-black cursor-pointer text-slate-600">
                    <p class="text-xs text-slate-550 leading-normal font-bold">Unggah foto karya seni, bukti turnamen, screenshot game, atau gambar pendukung (Format JPG/PNG, maks 5MB).</p>
                    
                    <!-- Preview block -->
                    <template x-if="imageUrl">
                        <div class="mt-3 p-2 rounded-2xl border border-slate-100 bg-slate-50 max-w-[240px] relative">
                            <img :src="imageUrl" class="w-full h-40 object-cover rounded-xl shadow-sm">
                            <button type="button" @click="imageUrl = null; $refs.imageInput.value = ''" 
                                    class="absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-md hover:bg-rose-700 transition">
                                <i class="ph-bold ph-xmark text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Attachment File Upload -->
                <div class="space-y-3">
                    <label class="block text-sm font-black text-slate-655 uppercase tracking-widest px-1">📎 Lampiran Dokumen Tambahan (PDF/ZIP, Opsional)</label>
                    <input type="file" name="attachment" 
                           class="w-full px-4 py-3 border-2 border-dashed border-slate-300 hover:border-indigo-300 rounded-2xl text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-slate-100 file:text-slate-700 file:font-black cursor-pointer text-slate-600">
                    <p class="text-xs text-slate-550 leading-normal font-bold">Lampirkan catatan pelajaran, proposal kepanitiaan, atau file game mod/aplikasi buatanmu (PDF, DOCS, ZIP, maks 10MB).</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-6 border-t border-slate-150">
                <a href="{{ route('forum.index') }}" 
                   class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-center hover:bg-slate-200 transition text-base">
                    Nggak Jadi
                </a>
                <button type="submit" 
                        class="flex-[2] py-4.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl font-black hover:shadow-lg transition duration-300 text-base shadow-md shadow-indigo-600/10">
                    Gaskan, Posting Sekarang! 🚀
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
