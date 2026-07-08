@extends('layouts.admin')

@section('title', 'Profil ' . $employee->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.employees.index') }}" class="p-2 bg-white/20 rounded-xl hover:bg-white/30 transition-all"><i class="fas fa-arrow-left"></i></a>
            <span class="text-sm text-white/70">Kembali ke Data Pegawai</span>
        </div>
        <div class="flex items-center gap-6">
            @if($employee->photo)
            <img src="{{ asset('storage/' . $employee->photo) }}" class="w-24 h-24 rounded-2xl object-cover border-4 border-white/30 shadow-lg">
            @else
            <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-4xl font-bold shadow-lg">
                {{ strtoupper(substr($employee->full_name, 0, 1)) }}
            </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold">{{ $employee->full_name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-white/80">
                    <span><i class="fas fa-id-badge mr-1"></i> {{ $employee->employee_code }}</span>
                    @if($employee->nip) <span><i class="fas fa-hashtag mr-1"></i> NIP: {{ $employee->nip }}</span> @endif
                    <span><i class="fas fa-school mr-1"></i> {{ $employee->school->name ?? '-' }}</span>
                    @if($employee->isYayasanStaff())
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold">STAF YAYASAN</span>
                    @endif
                </div>
                @if($teachingSchools->count() > 0)
                <div class="mt-2 text-sm text-white/70">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> Mengajar di:
                    @foreach($teachingSchools as $ts)
                    <span class="px-2 py-0.5 bg-white/15 rounded-lg text-xs ml-1">{{ $ts->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><p class="text-green-800 font-medium">{{ session('success') }}</p></div>
    </div>
    @endif

    <!-- Tabs -->
    <div x-data="{ tab: '{{ request('tab', 'pribadi') }}' }" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 flex flex-wrap gap-1 overflow-x-auto">
            @php
                $tabs = [
                    'pribadi' => ['📋', 'Data Pribadi'],
                    'keluarga' => ['👨‍👩‍👧‍👦', 'Keluarga'],
                    'penugasan' => ['🏫', 'Penugasan'],
                    'pendidikan' => ['📚', 'Pendidikan'],
                    'pelatihan' => ['🎓', 'Pelatihan'],
                    'dokumen' => ['📄', 'Dokumen'],
                    'kontrak' => ['📝', 'Kontrak'],
                    'kehadiran' => ['📅', 'Kehadiran'],
                ];
            @endphp
            @foreach($tabs as $key => [$icon, $label])
            <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50'"
                class="px-4 py-2.5 rounded-xl text-sm transition-all whitespace-nowrap">
                {{ $icon }} {{ $label }}
            </button>
            @endforeach
        </div>

        <!-- TAB: Data Pribadi -->
        <div x-show="tab === 'pribadi'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Informasi Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $fields = [
                        'Tempat, Tgl Lahir' => ($employee->birth_place ?? '-') . ', ' . ($employee->birth_date?->format('d M Y') ?? '-'),
                        'Jenis Kelamin' => $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                        'Agama' => ucfirst($employee->religion ?? '-'),
                        'Status Perkawinan' => ucfirst(str_replace('_', ' ', $employee->marital_status ?? '-')),
                        'Telepon' => $employee->phone ?? '-',
                        'Email' => $employee->email ?? '-',
                        'Alamat' => $employee->address ?? '-',
                        'TMT' => $employee->tmt_date?->format('d M Y') ?? '-',
                        'Status Kepegawaian' => ucfirst(str_replace('_', ' ', $employee->employment_status ?? '-')),
                        'Gaji Pokok' => 'Rp ' . number_format($employee->basic_salary ?? 0, 0, ',', '.'),
                        'Bank' => ($employee->bank_name ?? '-') . ' - ' . ($employee->bank_account ?? '-'),
                        'RFID UID' => $employee->rfid_uid ?? 'Belum terdaftar',
                    ];
                @endphp
                @foreach($fields as $label => $value)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-400 font-bold uppercase">{{ $label }}</p>
                    <p class="mt-1 text-gray-800 font-medium text-sm">{{ $value }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- TAB: Keluarga -->
        <div x-show="tab === 'keluarga'" class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Data Keluarga</h3>
                    <button onclick="document.getElementById('add-family-form').classList.toggle('hidden')"
                        class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all"><i class="fas fa-plus mr-1"></i> Tambah</button>
                </div>
                <!-- Add Form -->
                <div id="add-family-form" class="hidden mb-6 p-4 bg-indigo-50 rounded-xl">
                    <form action="{{ route('admin.employees.family.store', $employee) }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500 font-bold">Hubungan</label>
                            <select name="relation" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                                @foreach(\App\Models\EmployeeFamilyMember::RELATIONS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500 font-bold">Nama Lengkap</label>
                            <input type="text" name="full_name" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-bold">Gender</label>
                            <select name="gender" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                                <option value="L">L</option><option value="P">P</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-bold">Tgl Lahir</label>
                            <input type="date" name="birth_date" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                        </div>
                        <div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Simpan</button>
                        </div>
                    </form>
                </div>
                <!-- List -->
                @forelse($employee->familyMembers as $fm)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">{{ $fm->relation_label }}</span>
                        <span class="font-semibold text-gray-800">{{ $fm->full_name }}</span>
                        <span class="text-xs text-gray-500">{{ $fm->gender === 'L' ? '♂' : '♀' }} · {{ $fm->birth_date?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <form action="{{ route('admin.employees.family.destroy', $fm) }}" method="POST" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-red-400 hover:text-red-600 transition-colors"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">Belum ada data keluarga</p>
                @endforelse
            </div>
        </div>

        <!-- TAB: Penugasan (Riwayat Jabatan + Mengajar per Tahun Ajaran) -->
        <div x-show="tab === 'penugasan'" class="space-y-6">
            <!-- Position History -->
            <div class="bg-white rounded-2xl shadow-sm border border-purple-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-user-tag mr-2 text-purple-500"></i>Riwayat Jabatan</h3>
                @forelse($positionHistory as $year => $positions)
                <div class="mb-4">
                    <div class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold inline-block mb-2">{{ $year }}</div>
                    @foreach($positions as $pos)
                    <div class="ml-4 flex items-center gap-3 p-3 border-l-4 border-purple-200 bg-gray-50 rounded-r-xl mb-2">
                        <div class="flex-1">
                            <span class="font-semibold text-gray-800">{{ $pos->position->name ?? '-' }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ $pos->start_date?->format('d/m/Y') }} - {{ $pos->end_date?->format('d/m/Y') ?? 'Sekarang' }}</span>
                            @if($pos->sk_number) <span class="ml-2 text-xs text-gray-400">SK: {{ $pos->sk_number }}</span> @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">Belum ada riwayat jabatan</p>
                @endforelse
            </div>

            <!-- Teaching History -->
            @if($teachingHistory->count())
            <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-chalkboard-teacher mr-2 text-green-500"></i>Riwayat Mengajar</h3>
                @foreach($teachingHistory as $year => $assignments)
                <div class="mb-4">
                    <div class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold inline-block mb-2">{{ $year }}</div>
                    <div class="ml-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($assignments as $ta)
                        <div class="flex items-center gap-2 p-3 border-l-4 border-green-200 bg-gray-50 rounded-r-xl">
                            <span class="font-semibold text-gray-800 text-sm">{{ $ta->subject->name ?? '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $ta->classroom->class_name ?? '-' }}</span>
                            @if($ta->classroom?->school_id !== $employee->school_id)
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full">{{ $ta->classroom->school->name ?? 'Lintas' }}</span>
                            @endif
                            <span class="ml-auto text-xs text-gray-400">{{ $ta->hours_per_week }}j/mg</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- TAB: Pendidikan -->
        <div x-show="tab === 'pendidikan'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Riwayat Pendidikan</h3>
                <button onclick="document.getElementById('add-edu-form').classList.toggle('hidden')"
                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div id="add-edu-form" class="hidden mb-6 p-4 bg-indigo-50 rounded-xl">
                <form action="{{ route('admin.employees.educations.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Jenjang</label>
                        <select name="education_level" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                            @foreach(\App\Models\EmployeeEducation::LEVELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 font-bold">Institusi</label>
                        <input type="text" name="institution_name" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm" placeholder="Nama universitas/sekolah">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Jurusan</label>
                        <input type="text" name="major" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Tahun Lulus</label>
                        <input type="number" name="graduation_year" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm" min="1950" max="{{ now()->year + 5 }}">
                    </div>
                    <div class="md:col-span-5 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
            @forelse($employee->educations as $edu)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">{{ $edu->education_level }}</span>
                    <div>
                        <span class="font-semibold text-gray-800">{{ $edu->institution_name }}</span>
                        @if($edu->major) <span class="text-xs text-gray-500 ml-1">— {{ $edu->major }}</span> @endif
                    </div>
                    @if($edu->graduation_year) <span class="text-xs text-gray-400">({{ $edu->graduation_year }})</span> @endif
                </div>
                <form action="{{ route('admin.employees.educations.destroy', $edu) }}" method="POST" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="p-1.5 text-red-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Belum ada data pendidikan</p>
            @endforelse
        </div>

        <!-- TAB: Pelatihan -->
        <div x-show="tab === 'pelatihan'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Riwayat Pelatihan</h3>
                <button onclick="document.getElementById('add-training-form').classList.toggle('hidden')"
                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div id="add-training-form" class="hidden mb-6 p-4 bg-indigo-50 rounded-xl">
                <form action="{{ route('admin.employees.trainings.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 font-bold">Nama Pelatihan</label>
                        <input type="text" name="training_name" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Jenis</label>
                        <select name="training_type" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                            @foreach(\App\Models\EmployeeTraining::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Penyelenggara</label>
                        <input type="text" name="organizer" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Tanggal Mulai</label>
                        <input type="date" name="start_date" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Total Jam</label>
                        <input type="number" name="hours" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm" min="1">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
            @forelse($employee->trainings as $tr)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">{{ $tr->type_label }}</span>
                    <div>
                        <span class="font-semibold text-gray-800">{{ $tr->training_name }}</span>
                        <span class="text-xs text-gray-500 ml-1">{{ $tr->organizer ? '— ' . $tr->organizer : '' }}</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $tr->start_date->format('M Y') }} @if($tr->hours)· {{ $tr->hours }}j @endif</span>
                </div>
                <form action="{{ route('admin.employees.trainings.destroy', $tr) }}" method="POST" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="p-1.5 text-red-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Belum ada data pelatihan</p>
            @endforelse
        </div>

        <!-- TAB: Dokumen -->
        <div x-show="tab === 'dokumen'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Dokumen Pegawai</h3>
                <button onclick="document.getElementById('add-doc-form').classList.toggle('hidden')"
                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all"><i class="fas fa-upload mr-1"></i> Upload</button>
            </div>
            <div id="add-doc-form" class="hidden mb-6 p-4 bg-indigo-50 rounded-xl">
                <form action="{{ route('admin.employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Jenis Dokumen</label>
                        <select name="document_type" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                            @foreach(\App\Models\EmployeeDocument::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Nama Dokumen</label>
                        <input type="text" name="document_name" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm" placeholder="cth: KTP A.N. ...">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">File</label>
                        <input type="file" name="file" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Upload</button>
                    </div>
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse($employee->documents as $doc)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center"><i class="fas fa-file-alt text-blue-600"></i></div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">{{ $doc->document_name }}</div>
                            <div class="text-xs text-gray-500">{{ $doc->type_label }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-1.5 text-blue-500 hover:text-blue-700"><i class="fas fa-download text-sm"></i></a>
                        <form action="{{ route('admin.employees.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-red-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-2 text-center py-8 text-gray-400"><i class="fas fa-folder-open text-3xl mb-2"></i><p class="text-sm">Belum ada dokumen</p></div>
                @endforelse
            </div>
        </div>

        <!-- TAB: Kontrak -->
        <div x-show="tab === 'kontrak'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Kontrak Kerja</h3>
                <button onclick="document.getElementById('add-contract-form').classList.toggle('hidden')"
                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div id="add-contract-form" class="hidden mb-6 p-4 bg-indigo-50 rounded-xl">
                <form action="{{ route('admin.employees.contracts.store', $employee) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 font-bold">No. Kontrak</label>
                        <input type="text" name="contract_number" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Jenis</label>
                        <select name="contract_type" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                            @foreach(\App\Models\EmployeeContract::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Mulai</label>
                        <input type="date" name="start_date" required class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-bold">Berakhir</label>
                        <input type="date" name="end_date" class="w-full mt-1 px-3 py-2 bg-white border-none rounded-lg text-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
            @forelse($employee->contracts as $ct)
            <div class="flex items-center justify-between p-4 {{ $ct->is_active ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }} rounded-xl mb-2">
                <div class="flex items-center gap-3">
                    @if($ct->is_active) <span class="px-2 py-1 bg-green-200 text-green-800 text-[10px] font-bold rounded-full">AKTIF</span>
                    @else <span class="px-2 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold rounded-full">NONAKTIF</span> @endif
                    <div>
                        <span class="font-semibold text-gray-800">{{ $ct->contract_number }}</span>
                        <span class="text-xs text-gray-500 ml-2">{{ $ct->type_label }}</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $ct->start_date->format('d/m/Y') }} - {{ $ct->end_date?->format('d/m/Y') ?? '∞' }}</span>
                    @if($ct->isExpiringSoon()) <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full animate-pulse">SEGERA BERAKHIR</span> @endif
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Belum ada data kontrak</p>
            @endforelse
        </div>

        <!-- TAB: Kehadiran -->
        <div x-show="tab === 'kehadiran'" class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Kehadiran Bulan Ini ({{ now()->translatedFormat('F Y') }})</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $attStats = ['hadir' => ['Hadir', 'green'], 'sakit' => ['Sakit', 'yellow'], 'izin' => ['Izin', 'blue'], 'alpha' => ['Alpha', 'red']];
                @endphp
                @foreach($attStats as $key => [$label, $color])
                <div class="p-4 bg-{{ $color }}-50 rounded-xl text-center">
                    <p class="text-3xl font-bold text-{{ $color }}-600">{{ $attendanceSummary[$key] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('admin.employees.attendance.rekap', ['school_id' => $employee->school_id]) }}"
                    class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">Lihat rekapitulasi lengkap →</a>
            </div>
        </div>
    </div>
</div>
@endsection
