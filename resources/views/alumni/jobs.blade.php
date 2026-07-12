@extends(auth()->user()->layout)
@section('title', 'Lowongan Kerja (Job Board)')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-briefcase text-amber-500"></i> Papan Lowongan Kerja (*Job Board*)
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                SMKS Swasta Pembda Nias — Menghubungkan Alumni & Siswa Tingkat Akhir dengan DUDI Mitra
            </p>
        </div>
        <div>
            <a href="{{ route('alumni.tracer.form') }}" class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                <i class="fas fa-graduation-cap text-xs"></i> Isi Tracer Study (BMW)
            </a>
        </div>
    </div>

    {{-- Search / Filter Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Cari posisi kerja, nama perusahaan, atau keahlian..." class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-450 transition">
            </div>
            <button class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow flex items-center justify-center gap-2">
                <i class="fas fa-filter text-xs"></i> Filter Lowongan
            </button>
        </div>
    </div>

    {{-- Job Listings --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($jobs as $job)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-150 p-5 md:p-6 hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    {{-- Company name & salary badge --}}
                    <div class="flex justify-between items-start gap-3 mb-3">
                        <div>
                            <span class="text-[10px] bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                                {{ $job->company_name }}
                            </span>
                            <h3 class="text-base font-extrabold text-gray-850 mt-1 leading-snug">{{ $job->title }}</h3>
                        </div>
                        @if($job->salary_range)
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-lg text-xs font-bold whitespace-nowrap">
                                <i class="fas fa-coins text-[10px] mr-1"></i>{{ $job->salary_range }}
                            </span>
                        @else
                            <span class="bg-gray-50 text-gray-400 border border-gray-100 px-2 py-0.5 rounded-lg text-[10px] italic whitespace-nowrap">
                                Gaji Kompetitif
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="text-xs text-gray-600 space-y-3 mt-4">
                        <div>
                            <p class="font-bold text-gray-700 mb-1">Deskripsi Pekerjaan:</p>
                            <p class="leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
                        </div>

                        {{-- Requirements --}}
                        @if($job->requirements)
                            <div class="border-t border-gray-50 pt-3">
                                <p class="font-bold text-gray-700 mb-1">Persyaratan:</p>
                                <p class="leading-relaxed whitespace-pre-line">{{ $job->requirements }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Contact Info --}}
                <div class="border-t border-gray-50 pt-4 mt-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-[10px] text-gray-450">
                        Ditayangkan pada: {{ $job->created_at->translatedFormat('d M Y') }}
                    </div>
                    <div class="flex items-center gap-2">
                        @if($job->contact_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $job->contact_phone) }}" target="_blank" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-3 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                                <i class="fab fa-whatsapp"></i> Hubungi WA
                            </a>
                        @endif
                        @if($job->contact_email)
                            <a href="mailto:{{ $job->contact_email }}?subject=Lamaran Kerja: {{ $job->title }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                                <i class="far fa-envelope"></i> Kirim Email
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center text-gray-450 italic">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-briefcase text-2xl text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Belum ada lowongan pekerjaan aktif saat ini.</p>
                <p class="text-xs text-gray-400 mt-1">Kami akan mengabari Anda jika mitra DUDI membuka lowongan baru!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
