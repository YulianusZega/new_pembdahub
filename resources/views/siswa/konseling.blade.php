@extends('layouts.siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-pink-600 text-xl">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Catatan Perkembangan</h1>
                <p class="text-gray-500">Rekam jejak prestasi, pembinaan, dan bimbingan karakter.</p>
            </div>
        </div>
    </div>

    <!-- Content - Counseling Records -->
    <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">Catatan Konseling & Pembinaan</h2>
    <div class="space-y-6">
        @forelse($counselingRecords as $record)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Icon / Date -->
                    <div class="flex-shrink-0 flex md:flex-col items-center gap-2 md:w-24">
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl flex items-center justify-center text-2xl md:text-3xl {{ $record->record_type === 'penghargaan' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600' }}">
                            <i class="fas {{ $record->record_type === 'penghargaan' ? 'fa-trophy' : 'fa-exclamation-circle' }}"></i>
                        </div>
                        <div class="text-center">
                            <p class="font-bold text-gray-900 text-sm">{{ $record->incident_date->format('d M') }}</p>
                            <p class="text-xs text-gray-500">{{ $record->incident_date->format('Y') }}</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 space-y-3">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $record->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <!-- Category Badge -->
                                    @php
                                        $catColors = [
                                            'akademik' => 'bg-blue-100 text-blue-800',
                                            'perilaku' => 'bg-red-100 text-red-800',
                                            'sosial' => 'bg-green-100 text-green-800',
                                            'karir' => 'bg-purple-100 text-purple-800',
                                            'pribadi' => 'bg-orange-100 text-orange-800',
                                            'olahraga' => 'bg-cyan-100 text-cyan-800',
                                            'seni' => 'bg-pink-100 text-pink-800',
                                            'keagamaan' => 'bg-emerald-100 text-emerald-800',
                                        ];
                                        $colorClass = $catColors[$record->category] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                        {{ ucfirst($record->category) }}
                                    </span>

                                    <!-- Level / Severity Badge -->
                                    @if($record->record_type === 'penghargaan')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>{{ ucfirst($record->achievement_level) }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ match($record->severity) { 'ringan' => 'bg-green-100 text-green-800', 'sedang' => 'bg-yellow-100 text-yellow-800', 'berat' => 'bg-orange-100 text-orange-800', 'kritis' => 'bg-red-100 text-red-800', default => 'bg-gray-100 text-gray-800' } }}">
                                            {{ ucfirst($record->severity) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!-- Status Badge -->
                            <span class="px-3 py-1 rounded-full text-xs font-medium border {{ match($record->status) { 'selesai' => 'bg-green-50 text-green-700 border-green-200', 'tindak_lanjut' => 'bg-blue-50 text-blue-700 border-blue-200', default => 'bg-gray-50 text-gray-600 border-gray-200' } }}">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $record->description }}</p>

                        <!-- Action Taken -->
                        @if($record->action_taken)
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-1">Tindak Lanjut</p>
                            <p class="text-sm text-gray-700">{{ $record->action_taken }}</p>
                        </div>
                        @endif

                        <!-- Attachment -->
                        @if($record->attachment)
                            @php
                                $ext = strtolower(pathinfo($record->attachment, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                $isPdf = $ext === 'pdf';
                            @endphp
                            
                            <div class="mt-3">
                                @if($isImage)
                                    <div class="rounded-xl overflow-hidden border border-gray-200 max-w-sm">
                                        <img src="{{ asset('storage/' . $record->attachment) }}" alt="Bukti" class="w-full h-auto">
                                    </div>
                                @elseif($isPdf)
                                    <a href="{{ asset('storage/' . $record->attachment) }}" target="_blank" class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-100 hover:bg-red-100 transition group w-fit">
                                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:bg-red-200 transition">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-xs font-bold text-red-900">Dokumen PDF</p>
                                            <p class="text-xs text-red-600">Klik untuk melihat</p>
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $record->attachment) }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:underline">
                                        <i class="fas fa-paperclip mr-2"></i> Lihat Lampiran
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Footer: Counselor -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span>Dilaporkan oleh: <span class="font-medium text-gray-700">{{ $record->counselor->name ?? 'Admin' }}</span></span>
                <span>{{ $record->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 transform rotate-12">
                <i class="fas fa-clipboard-check text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Belum Ada Catatan</h3>
            <p class="text-gray-500 text-sm mt-1">Belum ada catatan perkembangan atau prestasi untuk saat ini.</p>
        </div>
        @endforelse
    </div>
</div>

    <!-- Content - Achievements -->
    <h2 class="text-xl font-bold text-gray-900 mt-10 mb-4">Prestasi & Penghargaan</h2>
    <div class="space-y-6">
        @forelse($achievements as $achievement)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-shrink-0 flex md:flex-col items-center gap-2 md:w-24">
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl flex items-center justify-center text-2xl md:text-3xl bg-blue-100 text-blue-600">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="text-center">
                            <p class="font-bold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($achievement->achievement_date)->format('d M') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($achievement->achievement_date)->format('Y') }}</p>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $achievement->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase">{{ $achievement->type }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase">{{ $achievement->level }}</span>
                                </div>
                            </div>
                        </div>

                        @if($achievement->description)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $achievement->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span>Tahun Ajaran: <span class="font-medium text-gray-700">{{ $achievement->academicYear->year ?? '-' }}</span></span>
                <span>{{ $achievement->created_at ? $achievement->created_at->diffForHumans() : '' }}</span>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 transform rotate-12">
                <i class="fas fa-award text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Belum Ada Prestasi</h3>
            <p class="text-gray-500 text-sm mt-1">Belum ada catatan prestasi untuk saat ini.</p>
        </div>
        @endforelse
    </div>
@endsection
