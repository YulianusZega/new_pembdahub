@extends('layouts.admin')
@section('title', 'Detail Catatan Perkembangan')
@section('content')
<div class="space-y-8 w-full max-w-full px-2 sm:px-6 pb-12">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-200">
                    <i class="fas fa-file-alt text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $record->title }}</h1>
                    <p class="text-base font-bold text-slate-600 mt-1">{{ $record->student->full_name ?? '-' }} — {{ $record->incident_date ? $record->incident_date->format('d M Y') : '-' }}</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.counseling.edit', ['record' => $record->id]) }}" class="inline-flex items-center px-5 py-3 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 shadow-md transition"><i class="fas fa-edit mr-2"></i>Edit</a>
                <a href="{{ route('admin.counseling.index') }}" class="inline-flex items-center px-5 py-3 bg-white border-2 border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition shadow-sm"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="xl:col-span-8 space-y-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Peristiwa</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $record->description }}</p>
            </div>

            @if($record->action_taken)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Tindak Lanjut</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $record->action_taken }}</p>
            </div>
            @endif

            @if($record->parent_notes)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Catatan Orang Tua</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $record->parent_notes }}</p>
            </div>
            @endif

            @if($record->attachment)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Dokumen Pendukung</h2>
                <div class="flex items-center gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 text-2xl shadow-sm">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $record->attachment_name ?? basename($record->attachment) }}</h4>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Dokumen Terlampir • {{ strtoupper(pathinfo($record->attachment, PATHINFO_EXTENSION)) }}</p>
                    </div>
                    <a href="{{ asset('storage/' . $record->attachment) }}" target="_blank" class="ml-auto px-5 py-2.5 bg-white border-2 border-gray-100 rounded-xl text-xs font-semibold text-gray-600 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
                        <i class="fas fa-external-link-alt mr-2"></i> Buka File
                    </a>
                </div>

                <!-- Premium Inline Preview -->
                @php
                    $url = asset('storage/' . $record->attachment);
                    $ext = pathinfo($record->attachment, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $isPdf = strtolower($ext) === 'pdf';
                @endphp

                <div class="relative bg-slate-100 rounded-2xl border-4 border-slate-50 overflow-hidden shadow-inner">
                    @if($isImage)
                        <img src="{{ $url }}" alt="Preview" class="w-full h-auto max-h-[600px] object-contain mx-auto">
                    @elseif($isPdf)
                        <iframe src="{{ $url }}#toolbar=0" class="w-full h-[600px] border-0"></iframe>
                    @else
                        <div class="py-20 text-center">
                            <i class="fas fa-file-download text-4xl text-slate-300 mb-4"></i>
                            <p class="text-sm font-bold text-slate-400">Preview tidak tersedia untuk format ini.</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="xl:col-span-4 space-y-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Informasi</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Siswa</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $record->student->full_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Kategori</dt>
                        <dd>
                            @php
                                $catColors = [
                                    'akademik' => 'bg-blue-100 text-blue-800',
                                    'perilaku' => 'bg-red-100 text-red-800',
                                    'sosial' => 'bg-green-100 text-green-800',
                                    'karir' => 'bg-purple-100 text-purple-800',
                                    'pribadi' => 'bg-orange-100 text-orange-800',
                                ];
                                $colorClass = $catColors[$record->category] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-lg {{ $colorClass }}">{{ ucfirst($record->category) }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">{{ $record->record_type === 'penghargaan' ? 'Tingkat Prestasi' : 'Severity' }}</dt>
                        <dd>
                            @if($record->record_type === 'penghargaan')
                                <span class="px-2 py-1 text-xs rounded-lg bg-blue-100 text-blue-800 font-bold">{{ ucfirst($record->achievement_level ?? '-') }}</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-lg font-semibold @if($record->severity === 'kritis') bg-red-100 text-red-800 @elseif($record->severity === 'berat') bg-orange-100 text-orange-800 @elseif($record->severity === 'sedang') bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif">{{ ucfirst($record->severity) }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd><span class="px-2 py-1 text-xs rounded-lg @if($record->status === 'selesai') bg-green-100 text-green-800 @elseif($record->status === 'tindak_lanjut') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</span></dd>
                    </div>
                    @if($record->follow_up_date)
                    <div>
                        <dt class="text-xs text-gray-500">Follow-up</dt>
                        <dd class="text-sm text-gray-700">{{ $record->follow_up_date->format('d M Y') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500">Rahasia</dt>
                        <dd class="text-sm text-gray-700">{{ $record->is_confidential ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Dilaporkan oleh</dt>
                        <dd class="text-sm text-gray-700">{{ $record->counselor->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- ACTION SECTION (Status Update) -->
            @if($record->record_type !== 'penghargaan' && in_array($record->student->status, ['aktif', 'skorsing']))
            <div class="bg-white rounded-2xl shadow-lg p-6 mt-6 border-l-4 border-red-500">
                <h3 class="text-sm font-semibold text-red-600 uppercase mb-4">Tindakan Lanjut Status Siswa</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Jika kasus ini memerlukan perubahan status siswa (seperti Skorsing atau Dikeluarkan), silakan proses di sini.
                </p>
                <button onclick="document.getElementById('actionModal').classList.remove('hidden')" 
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition shadow-md">
                    <i class="fas fa-gavel mr-2"></i> Proses Perubahan Status
                </button>
            </div>
            @endif

            <!-- TEMBUSAN & PARTISIPAN (CC) -->
            @if($record->participants->count() > 0)
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl shadow-lg p-6 mt-6 border border-indigo-500/20 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full blur-xl pointer-events-none"></div>
                
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300">
                            <i class="fas fa-share-nodes text-xs"></i>
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Tembusan & Partisipan</h3>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30 font-semibold">{{ $record->participants->count() }} Pihak</span>
                </div>

                <div class="space-y-3">
                    @foreach($record->participants as $part)
                        @php
                            $u = $part->user;
                            $t = $u ? $u->teacher : null;
                            $name = $t ? $t->full_name : ($u ? $u->name : 'Staff');
                            $phone = $t ? $t->phone : null;
                            $roleName = match($part->role) {
                                'wali_kelas' => 'Wali Kelas',
                                'pks' => 'Tim PKS / BK',
                                'guru_mapel' => $part->notes ?: 'Guru Pembimbing / Pelatih',
                                'lainnya' => $part->notes ?: 'Guru Piket / Terkait',
                                default => ucfirst(str_replace('_', ' ', $part->role))
                            };
                            $badgeColor = match($part->role) {
                                'wali_kelas' => 'bg-emerald-600 text-white border-emerald-700',
                                'pks' => 'bg-purple-600 text-white border-purple-700',
                                'guru_mapel' => 'bg-amber-600 text-white border-amber-700',
                                default => 'bg-pink-600 text-white border-pink-700'
                            };
                        @endphp
                        <div class="p-4 bg-white rounded-xl border-2 border-slate-300 flex items-center justify-between gap-3 shadow-md">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded shadow-sm border {{ $badgeColor }} uppercase tracking-wider">{{ $roleName }}</span>
                                </div>
                                <p class="text-sm sm:text-base font-black text-slate-950 truncate">{{ $name }}</p>
                            </div>
                            @if($phone)
                                @php
                                    $waPhone = preg_replace('/^0/', '62', preg_replace('/\D/', '', $phone));
                                    $waMsg = urlencode("Assalamu'alaikum Yth. Ibu/Bapak {$name},\n\nSebagai tembusan ({$roleName}), berikut info catatan perkembangan siswa:\nNama: " . ($record->student->full_name ?? '-') . "\nHal: {$record->title}\n\nMohon cek di portal PembdaHUB.");
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" title="Chat WhatsApp" class="w-9 h-9 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-md flex-shrink-0 text-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- REKOMENDASI SECTION (Moved into Sidebar) -->
            @if($record->record_type !== 'penghargaan')
            <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Rekomendasi Tindak Lanjut</h3>
                
                @php
                    $student = $record->student;
                    $classroom = $student ? $student->currentClassroom()->first() : null;
                    $homeroom = $classroom ? $classroom->homeroomTeacher : null;
                    $principal = $student && $student->school ? $student->school->principal : null;
                    
                    $message = "Assalamu'alaikum, Yth. [NAME],\n\n" .
                               "Mohon perhatian untuk siswa:\n" .
                               "Nama: " . ($student->full_name ?? '-') . "\n" .
                               "Kelas: " . ($classroom->class_name ?? '-') . "\n" .
                               "Kasus: " . $record->title . " (" . ucfirst($record->record_type) . ")\n" .
                               "Tanggal: " . ($record->incident_date ? $record->incident_date->format('d M Y') : '-') . "\n\n" .
                               "Mohon cek di sistem PembdaHub untuk detail dan tindak lanjut.\n" .
                               "Terima kasih.";
                @endphp

                <div class="space-y-4">
                    <!-- Wali Kelas -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Wali Kelas</span>
                            @if($homeroom)
                                <span class="text-xs font-medium text-gray-900">{{ $homeroom->full_name }}</span>
                            @else
                                <span class="text-xs text-red-500">Tidak ada data</span>
                            @endif
                        </div>
                        @if($homeroom && $homeroom->phone)
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $homeroom->phone)) }}?text={{ urlencode(str_replace('[NAME]', $homeroom->full_name, $message)) }}" 
                               target="_blank"
                               class="flex items-center justify-center w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                                <i class="fab fa-whatsapp mr-2"></i> Chat Wali Kelas
                            </a>
                        @else
                            <button disabled class="flex items-center justify-center w-full px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed text-sm">
                                <i class="fab fa-whatsapp mr-2"></i> No HP Tidak Tersedia
                            </button>
                        @endif
                    </div>

                    <hr class="border-gray-100">

                    <!-- Kepala Sekolah -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Kepala Sekolah</span>
                            @if($principal)
                                <span class="text-xs font-medium text-gray-900">{{ $principal->full_name }}</span>
                            @else
                                <span class="text-xs text-red-500">Tidak ada data</span>
                            @endif
                        </div>
                        @if($principal && $principal->phone)
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $principal->phone)) }}?text={{ urlencode(str_replace('[NAME]', $principal->full_name, $message)) }}" 
                               target="_blank"
                               class="flex items-center justify-center w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                                <i class="fab fa-whatsapp mr-2"></i> Chat Kepsek
                            </a>
                        @else
                            <button disabled class="flex items-center justify-center w-full px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed text-sm">
                                <i class="fab fa-whatsapp mr-2"></i> No HP Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div> <!-- End Sidebar -->
    </div> <!-- End Grid -->
</div> <!-- End Root Space-y-6 -->

    <!-- Action Modal (Outside Grid, Inside Content or Root, absolute position ok) -->
    <div id="actionModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Proses Perubahan Status</h3>
                <button onclick="document.getElementById('actionModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.counseling.action', ['record' => $record->id]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Tindakan</label>
                    <select name="action_type" id="actionType" class="w-full rounded-xl border-gray-300 focus:ring-red-500 p-2.5" onchange="toggleDuration()">
                        <option value="skorsing">Skorsing (Suspended)</option>
                        <option value="dikeluarkan">Dikeluarkan (Drop Out)</option>
                        <option value="keluar">Keluar (Withdrawn)</option>
                        <option value="pindah">Pindah Sekolah</option>
                    </select>
                </div>

                <div class="mb-4" id="durationField">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Durasi (Hari)</label>
                    <input type="number" name="duration_days" min="1" class="w-full rounded-xl border-gray-300 focus:ring-red-500 p-2.5" placeholder="Contoh: 3">
                    <p class="text-xs text-gray-500 mt-1">Hanya untuk skorsing.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alasan / Keterangan</label>
                    <textarea name="reason" rows="3" required class="w-full rounded-xl border-gray-300 focus:ring-red-500 p-2.5" placeholder="Jelaskan alasan tindakan ini..."></textarea>
                </div>
                
                <input type="hidden" name="notes" value="Diproses melalui Konseling: {{ $record->title }}">

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('actionModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">Simpan & Proses</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleDuration() {
            var action = document.getElementById('actionType').value;
            var field = document.getElementById('durationField');
            if (action === 'skorsing') {
                field.style.display = 'block';
            } else {
                field.style.display = 'none';
            }
        }
    </script>
@endsection
