@extends('layouts.admin')

@section('title', 'PSB - Penerimaan Siswa Baru')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-user-plus"></i> Penerimaan Siswa Baru (PSB)
                </h1>
                <p class="text-white/70 text-sm mt-1">Kelola pendaftaran calon siswa baru semua jenjang</p>
            </div>
            <a href="{{ route('admin.psb.applicants.create') }}" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                <i class="fas fa-plus"></i> Pendaftaran Baru
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @if(auth()->user()->isSuperAdmin() || auth()->user()->school_id == 1)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $smpCount }}</p>
                    <p class="text-xs text-gray-500">SMPS Pembda 2</p>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->isSuperAdmin() || auth()->user()->school_id == 2)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $smaCount }}</p>
                    <p class="text-xs text-gray-500">SMA Pembda 1</p>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->isSuperAdmin() || auth()->user()->school_id == 3)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-wrench"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $smkCount }}</p>
                    <p class="text-xs text-gray-500">SMKS Pembda Nias</p>
                </div>
            </div>
        </div>
        @endif
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalApplicants }}</p>
                    <p class="text-xs text-gray-500">Total Keseluruhan</p>
                </div>
            </div>
        </div>
    </div>


    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-filter text-teal-600"></i> Filter & Pencarian
            </h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.psb.applicants.export') }}?format=excel&{{ http_build_query(request()->except('page')) }}" 
                class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-medium hover:bg-emerald-100 transition flex items-center gap-1">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('admin.psb.applicants.export') }}?format=pdf&{{ http_build_query(request()->except('page')) }}" 
                class="px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-xs font-medium hover:bg-rose-100 transition flex items-center gap-1">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.psb.applicants.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Tahun</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>{{ $ay->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sekolah</label>
                    <select name="school_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Program Keahlian</label>
                    <select name="program_keahlian_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Program</option>
                        @foreach($programKeahlians as $program)
                            <option value="{{ $program->id }}" {{ request('program_keahlian_id') == $program->id ? 'selected' : '' }}>{{ $program->kode }} - {{ $program->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="payment_verified" {{ request('status') == 'payment_verified' ? 'selected' : '' }}>Bayar Terverifikasi</option>
                        <option value="document_verified" {{ request('status') == 'document_verified' ? 'selected' : '' }}>Dokumen OK</option>
                        <option value="tested" {{ request('status') == 'tested' ? 'selected' : '' }}>Sudah Tes</option>
                        <option value="scored" {{ request('status') == 'scored' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="reregistered" {{ request('status') == 'reregistered' ? 'selected' : '' }}>Daftar Ulang</option>
                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Siswa Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama, NISN, No. Reg..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl text-sm font-medium hover:bg-teal-700 transition flex items-center gap-1">
                    <i class="fas fa-search text-xs"></i> Cari
                </button>
                <a href="{{ route('admin.psb.applicants.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition flex items-center gap-1">
                    <i class="fas fa-redo text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Reg</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Pendaftar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sekolah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program/Konsentrasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jalur</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($applicants as $index => $applicant)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="text-xs font-bold text-gray-500">{{ $applicants->firstItem() + $index }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs font-medium text-gray-900">{{ $applicant->registration_number }}</span>
                                <p class="text-[10px] text-gray-400">{{ $applicant->submission_date?->format('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-12 h-12 rounded-full overflow-hidden ring-2 ring-teal-100 shadow-sm flex-shrink-0">
                                        @if($applicant->photo_path)
                                            <img src="{{ Storage::url($applicant->photo_path) }}" 
                                                 alt="{{ $applicant->full_name }}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-full h-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-bold" style="display:none;">
                                                {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $applicant->full_name }}</p>
                                        <p class="text-[10px] text-gray-400">NISN: {{ $applicant->nisn }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="text-xs text-gray-900">{{ $applicant->school->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $applicant->academicYear->year }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($applicant->programKeahlian || $applicant->konsentrasiKeahlian)
                                    <div class="space-y-1">
                                        @if($applicant->programKeahlian)
                                            <div class="flex items-center gap-1">
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold">{{ $applicant->programKeahlian->kode }}</span>
                                                <span class="text-[10px] text-gray-600">{{ $applicant->programKeahlian->nama }}</span>
                                            </div>
                                        @endif
                                        @if($applicant->konsentrasiKeahlian)
                                            <div class="flex items-center gap-1">
                                                <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-bold">{{ $applicant->konsentrasiKeahlian->kode }}</span>
                                                <span class="text-[10px] text-gray-600">{{ $applicant->konsentrasiKeahlian->nama }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[10px] text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $applicant->admission_path === 'prestasi' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($applicant->admission_path) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-{{ $applicant->getStatusBadgeColor() }}-100 text-{{ $applicant->getStatusBadgeColor() }}-800">
                                    {{ $applicant->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('admin.psb.applicants.show', $applicant->id) }}" 
                                   class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-sm">Tidak ada data pendaftar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applicants->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $applicants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
