@extends('layouts.admin')

@section('title', 'Detail Pendaftar PSB')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative">
            <div class="flex items-center text-sm text-white/60 mb-3">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white/90 transition">Dashboard</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.psb.applicants.index') }}" class="hover:text-white/90 transition">PSB</a>
                <span class="mx-2">/</span>
                <span class="text-white font-semibold">{{ $applicant->registration_number }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <i class="fas fa-user-circle"></i> {{ $applicant->full_name }}
                    </h1>
                    <div class="flex items-center gap-3 mt-2 text-sm text-white/70">
                        <span><i class="fas fa-hashtag mr-1"></i>{{ $applicant->registration_number }}</span>
                        <span><i class="fas fa-school mr-1"></i>{{ $applicant->school->name }}</span>
                        <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs font-medium">{{ $applicant->getStatusLabel() }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.psb.applicants.index') }}" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-lg">
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- VISUAL FLOW TRACKER COMPONENT --}}
    <x-admin.psb.flow-tracker :applicant="$applicant" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Data Pribadi & Orang Tua --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Data Pribadi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm"><i class="fas fa-user"></i></span>
                    Data Pribadi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    {{-- Data di kiri (2 kolom) --}}
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">NISN</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $applicant->nisn }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Jenis Pendaftaran</p>
                            <p class="text-sm font-semibold">
                                @if($applicant->registration_type === 'offline')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs"><i class="fas fa-building mr-1"></i> Offline</span>
                                    @if($applicant->registered_by)
                                        <span class="text-xs text-gray-500 ml-2">oleh {{ $applicant->registered_by }}</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs"><i class="fas fa-globe mr-1"></i> Online</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Nama Lengkap</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $applicant->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Jenis Kelamin</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                    
                    {{-- Foto di kanan (1 kolom) --}}
                    <div class="flex justify-center items-start">
                        <div class="relative">
                            @if($applicant->photo_path)
                            <img src="{{ Storage::disk('public')->url($applicant->photo_path) }}" 
                                 alt="Foto {{ $applicant->full_name }}"
                                 class="w-44 h-44 rounded-3xl object-cover border-4 border-white shadow-lg ring-4 ring-emerald-50"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-44 h-44 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-3xl flex items-center justify-center text-4xl font-bold text-emerald-600 border-4 border-white shadow-lg ring-4 ring-emerald-50" style="display:none;">
                                {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
                            </div>
                            @else
                            <div class="w-44 h-44 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-3xl flex items-center justify-center text-4xl font-bold text-emerald-600 border-4 border-white shadow-lg ring-4 ring-emerald-50">
                                {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
                            </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white rounded-full p-2 shadow-lg z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    @if($applicant->wave)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Gelombang Pendaftaran</p>
                            <p class="text-sm font-semibold text-gray-700">
                                {{ $applicant->wave->name }}
                                <span class="text-xs text-gray-500">
                                    ({{ $applicant->wave->start_date->format('d M') }} - {{ $applicant->wave->end_date->format('d M Y') }})
                                </span>
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">Tempat, Tanggal Lahir</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->birth_place }}, {{ $applicant->birth_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Agama</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->religion }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Telepon</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->phone ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->email ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->address }}</p>
                    </div>
                    @if($applicant->programKeahlian || $applicant->konsentrasiKeahlian)
                    <div class="col-span-2 bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border-l-4 border-purple-400">
                        <p class="text-xs text-purple-600 font-semibold mb-3"><i class="fas fa-graduation-cap mr-1"></i> Program & Konsentrasi Keahlian (SMK)</p>
                        
                        @if($applicant->programKeahlian)
                        <div class="mb-3">
                            <p class="text-xs text-gray-500 mb-1">Program Keahlian</p>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg font-bold text-sm">{{ $applicant->programKeahlian->kode }}</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $applicant->programKeahlian->nama }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($applicant->konsentrasiKeahlian)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Konsentrasi Keahlian</p>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-purple-600 text-white rounded-lg font-bold text-sm">{{ $applicant->konsentrasiKeahlian->kode }}</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $applicant->konsentrasiKeahlian->nama }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Asal Sekolah</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->previous_school }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Orang Tua --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm"><i class="fas fa-users"></i></span>
                    Data Orang Tua
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Nama Ayah</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->father_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Telepon Ayah</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->father_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nama Ibu</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->mother_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Telepon Ibu</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->mother_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Dokumen yang Diupload --}}
            @if($applicant->documents && $applicant->documents->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-sm"><i class="fas fa-folder"></i></span>
                        Dokumen Pendaftaran
                    </h3>
                    <div class="space-y-3">
                        @foreach($applicant->documents as $doc)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                        <i class="fas fa-file-{{ $doc->document_type === 'pdf' ? 'pdf' : 'image' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $doc->label }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $doc->file_name }}</p>
                                        <p class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 2) }} KB • {{ $doc->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($doc->verified)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check text-green-500 mr-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-hourglass-half mr-1"></i> Pending
                                        </span>
                                    @endif
                                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" 
                                        class="px-3 py-1 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-700">
                            <strong>Total Dokumen:</strong> {{ $applicant->documents->count() }} dokumen
                            @if($applicant->documents->where('verified', true)->count() > 0)
                                | <strong class="text-green-600">{{ $applicant->documents->where('verified', true)->count() }} Verified</strong>
                            @endif
                            @if($applicant->documents->where('verified', false)->count() > 0)
                                | <strong class="text-yellow-600">{{ $applicant->documents->where('verified', false)->count() }} Pending</strong>
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- Data Prestasi (for prestasi path applicants) --}}
            @if($applicant->admission_path === 'prestasi' && $applicant->achievements && $applicant->achievements->count() > 0)
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-sm border border-amber-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fas fa-trophy"></i></span>
                        Data Prestasi — Verifikasi Admin
                        @if($applicant->status === 'submitted')
                            <span class="ml-3 px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-xs font-semibold animate-pulse">
                                <i class="fas fa-hourglass-half mr-1"></i> Menunggu Verifikasi
                            </span>
                        @elseif($applicant->prestasi_verified_at)
                            <span class="ml-3 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i> Diverifikasi {{ $applicant->prestasi_verified_at->format('d M Y H:i') }}
                            </span>
                        @endif
                    </h3>

                    @if($applicant->prestasi_rejection_reason)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700">
                                <strong><i class="fas fa-times-circle text-red-500 mr-1"></i> Prestasi Ditolak:</strong> {{ $applicant->prestasi_rejection_reason }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">Pendaftar dialihkan ke jalur reguler.</p>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @foreach($applicant->achievements as $achievement)
                            <div class="bg-white rounded-lg p-4 border border-amber-100 shadow-sm">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Nama Prestasi</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $achievement->achievement_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tipe</p>
                                        <p class="text-sm font-semibold text-gray-700">{{ ucfirst($achievement->achievement_type ?? '-') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tingkat</p>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $achievement->achievement_level === 'international' ? 'bg-purple-100 text-purple-700' : '' }} {{ $achievement->achievement_level === 'national' ? 'bg-blue-100 text-blue-700' : '' }} {{ $achievement->achievement_level === 'provincial' ? 'bg-green-100 text-green-700' : '' }} {{ $achievement->achievement_level === 'district' ? 'bg-yellow-100 text-yellow-700' : '' }} {{ $achievement->achievement_level === 'school' ? 'bg-gray-100 text-gray-700' : '' }}">
                                            {{ ucfirst($achievement->achievement_level) }}
                                        </span>
                                    </div>
                                    @if($achievement->rank)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Peringkat</p>
                                        <p class="text-sm font-bold text-amber-600">
                                            @if(in_array($achievement->rank, ['1','2','3']))
                                                <i class="fas fa-medal mr-1"></i> Juara {{ $achievement->rank }}
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $achievement->rank)) }}
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                    @if($achievement->organizer)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Penyelenggara / Asal Sekolah</p>
                                        <p class="text-sm font-semibold text-gray-700">{{ $achievement->organizer }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tahun</p>
                                        <p class="text-sm font-semibold text-gray-700">{{ $achievement->year }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Poin Prestasi</p>
                                        <p class="text-2xl font-bold text-amber-600">{{ number_format($achievement->points, 1) }}</p>
                                    </div>
                                </div>

                                {{-- Certificate preview --}}
                                @if($achievement->certificate_path)
                                    <div class="mt-4 pt-4 border-t border-amber-100">
                                        <p class="text-xs text-gray-500 mb-2"><i class="fas fa-file-alt mr-1"></i> Sertifikat / Bukti Prestasi</p>
                                        <div class="flex items-center gap-3">
                                            <a href="{{ Storage::disk('public')->url($achievement->certificate_path) }}" target="_blank" 
                                                class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600 transition-all">
                                                <i class="fas fa-eye mr-2"></i>Lihat Sertifikat
                                            </a>
                                            <a href="{{ Storage::disk('public')->url($achievement->certificate_path) }}" download 
                                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-all">
                                                <i class="fas fa-download mr-2"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-amber-100">
                                        <p class="text-xs text-red-500"><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Sertifikat belum diupload</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Fee Exemption Info --}}
                    @if($applicant->feeExemptions && $applicant->feeExemptions->count() > 0)
                        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-sm font-semibold text-green-800 mb-2"><i class="fas fa-coins mr-1"></i> Pembebasan Biaya Pendaftaran</p>
                            @foreach($applicant->feeExemptions as $exemption)
                                <div class="text-sm text-green-700">
                                    <p>Biaya Asli: <strong>Rp {{ number_format($exemption->original_fee_amount, 0, ',', '.') }}</strong></p>
                                    <p>Pembebasan: <strong>Rp {{ number_format($exemption->exemption_amount, 0, ',', '.') }}</strong></p>
                                    <p>Biaya Akhir: <strong class="text-green-800 text-lg">Rp {{ number_format($exemption->final_fee_amount, 0, ',', '.') }}</strong></p>
                                    @if($exemption->verified)
                                        <span class="inline-block mt-1 px-2 py-1 bg-green-200 text-green-800 text-xs rounded"><i class="fas fa-check-circle text-green-500 mr-1"></i> Verified</span>
                                    @else
                                        <span class="inline-block mt-1 px-2 py-1 bg-yellow-200 text-yellow-800 text-xs rounded"><i class="fas fa-hourglass-half mr-1"></i> Belum Diverifikasi</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Nilai & Ranking --}}
            @if($applicant->final_score)
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl shadow-sm border border-yellow-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-yellow-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fas fa-chart-bar"></i></span>
                        Hasil Penilaian
                    </h3>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Raport (40%)</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $applicant->raport_score ?? '-' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Tes (30%)</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $applicant->test_score ?? '-' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Interview (20%)</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $applicant->interview_score ?? '-' }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-600 mb-1">Prestasi (10%)</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $applicant->achievement_score ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t-2 border-yellow-200 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">NILAI AKHIR</p>
                            <p class="text-4xl font-bold text-yellow-700">{{ $applicant->final_score }}</p>
                        </div>
                        @if($applicant->ranking)
                            <div>
                                <p class="text-sm text-gray-600 text-right">RANKING</p>
                                <p class="text-4xl font-bold text-yellow-700">
                                    #{{ $applicant->ranking }}
                                    @if($applicant->ranking <= 3) <i class="fas fa-trophy mr-1"></i> @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Quick Actions & Info --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-bolt text-teal-600"></i> Aksi Cepat</h3>
                <div class="space-y-3">
                    @if($applicant->status === 'submitted')
                        @if($applicant->admission_path === 'prestasi')
                            {{-- PRESTASI PATH: Show verify/reject prestasi buttons --}}
                            <div class="p-4 bg-amber-50 rounded-lg border-2 border-amber-200 mb-2">
                                <p class="text-sm text-amber-800 font-semibold mb-1">
                                    <i class="fas fa-trophy mr-2"></i> Jalur Prestasi
                                </p>
                                <p class="text-xs text-amber-700">
                                    Pendaftar ini menggunakan jalur prestasi. Verifikasi data prestasi terlebih dahulu. 
                                    Jika disetujui, pendaftar akan langsung diminta upload dokumen (tanpa pembayaran).
                                </p>
                            </div>
                            <form action="{{ route('admin.psb.applicants.verify-prestasi', $applicant) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-lg hover:shadow-lg transition-all"
                                    onclick="return confirm('Apakah data prestasi sudah valid dan ingin diverifikasi?')">
                                    <i class="fas fa-check-circle mr-2"></i> Verifikasi Data Prestasi
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('rejectPrestasiModal').classList.remove('hidden')" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg hover:shadow-lg transition-all">
                                <i class="fas fa-times-circle mr-2"></i> Tolak Prestasi (Alihkan ke Reguler)
                            </button>
                        @else
                            {{-- REGULER PATH: Show verify payment button --}}
                            <form action="{{ route('admin.psb.applicants.verify-payment', $applicant) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition-all"
                                    onclick="return confirm('Apakah pembayaran sudah diterima dan ingin diverifikasi?')">
                                    <i class="fas fa-check-circle mr-2"></i> Verifikasi Pembayaran
                                </button>
                            </form>
                        @endif
                    @endif

                    @if($applicant->status === 'payment_verified' || $applicant->status === 'prestasi_verified')
                        <form action="{{ route('admin.psb.applicants.verify-document', $applicant) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all"
                                onclick="return confirm('Apakah semua dokumen sudah lengkap dan valid?')">
                                <i class="fas fa-file-check mr-2"></i> Verifikasi Dokumen
                            </button>
                        </form>
                        <form action="{{ route('admin.psb.notifications.send-document-request', $applicant) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-yellow-500 text-white rounded-lg hover:shadow-lg transition-all">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Permintaan Upload Dokumen
                            </button>
                        </form>
                    @endif

                    @if($applicant->status === 'document_verified')
                        @php
                            $requiresTest = $applicant->school->requires_test ?? false;
                        @endphp

                        @if($requiresTest)
                            {{-- School requires test - Show input score button --}}
                            <a href="{{ route('admin.psb.applicants.input-score', $applicant) }}" class="block w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white text-center rounded-lg hover:shadow-lg transition-all">
                                <i class="fas fa-edit mr-2"></i>Input Nilai {{ $applicant->school->test_type ?? 'Tes' }}
                            </a>
                        @else
                            {{-- School doesn't require test - Direct accept --}}
                            <div class="p-4 bg-blue-50 rounded-lg border-2 border-blue-200 mb-4">
                                <p class="text-sm text-blue-800 mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Sekolah ini tidak memerlukan tes masuk. Pendaftar dapat langsung diterima.
                                </p>
                            </div>
                            <form action="{{ route('admin.psb.applicants.accept', $applicant) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition-all"
                                    onclick="return confirm('Terima pendaftar ini?')">
                                    <i class="fas fa-check-double mr-2"></i>Terima Pendaftar (Tanpa Tes)
                                </button>
                            </form>
                        @endif
                    @endif

                    @if($applicant->status === 'scored')
                        <form action="{{ route('admin.psb.applicants.accept', $applicant) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition-all">
                                <i class="fas fa-check-double mr-2"></i>Terima Pendaftar
                            </button>
                        </form>
                        <form action="{{ route('admin.psb.applicants.reject', $applicant) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:shadow-lg transition-all"
                                onclick="return confirm('Yakin ingin menolak pendaftar ini?')">
                                <i class="fas fa-times-circle mr-2"></i>Tolak Pendaftar
                            </button>
                        </form>
                    @endif

                    @if($applicant->canMigrateToStudent())
                        <form action="{{ route('admin.psb.applicants.migrate', $applicant) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-lg transition-all animate-pulse">
                                <i class="fas fa-user-graduate mr-2"></i>Aktifkan sebagai Siswa
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.psb.applicants.edit', $applicant) }}" class="block w-full px-4 py-3 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition-all">
                        <i class="fas fa-edit mr-2"></i>Edit Data
                    </a>
                </div>
            </div>

            {{-- Info Sekolah & Jalur --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-school text-teal-600"></i> Info Pendaftaran</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Sekolah Tujuan</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->school->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tahun Ajaran</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $applicant->academicYear->year }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jalur Pendaftaran</p>
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full {{ $applicant->admission_path === 'prestasi' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($applicant->admission_path) }}
                        </span>
                    </div>
                    @if($applicant->major_choice_1)
                        <div>
                            <p class="text-xs text-gray-500">Pilihan Jurusan 1</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $applicant->majorChoice1->name ?? '-' }}</p>
                        </div>
                    @endif
                    @if($applicant->major_choice_2)
                        <div>
                            <p class="text-xs text-gray-500">Pilihan Jurusan 2</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $applicant->majorChoice2->name ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-teal-600"></i> Timeline</h3>
                <div class="space-y-3">
                    @if($applicant->submission_date)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Disubmit</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->submission_date->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->prestasi_verified_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-amber-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Prestasi Diverifikasi</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->prestasi_verified_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->payment_verified_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Pembayaran Diverifikasi</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->payment_verified_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->document_verified_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Dokumen Diverifikasi</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->document_verified_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->accepted_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Diterima</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->accepted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->rejected_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Ditolak</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->rejected_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($applicant->registered_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Terdaftar sebagai Siswa</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $applicant->registered_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Prestasi Modal --}}
@if($applicant->admission_path === 'prestasi' && $applicant->status === 'submitted')
<div id="rejectPrestasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl"><i class="fas fa-times-circle text-red-500"></i></span>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Tolak Data Prestasi</h3>
            <p class="text-sm text-gray-500 mt-2">Pendaftar akan dialihkan ke jalur reguler dan diminta membayar biaya pendaftaran.</p>
        </div>
        
        <form action="{{ route('admin.psb.applicants.reject-prestasi', $applicant) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label for="rejection_reason" class="block text-sm font-semibold text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="Contoh: Data raport tidak sesuai, sertifikat tidak valid, peringkat tidak memenuhi syarat..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('rejectPrestasiModal').classList.add('hidden')" 
                    class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-semibold">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all font-semibold"
                    onclick="return confirm('Yakin ingin menolak prestasi ini? Pendaftar akan dialihkan ke jalur reguler.')">
                    Tolak Prestasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
