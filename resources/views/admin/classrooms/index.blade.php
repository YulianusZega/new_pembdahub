@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    Ruang Kelas
                </h1>
                <p class="text-gray-600 mt-2 ml-1">Kelola ruang kelas dan siswa</p>
            </div>
            <a href="{{ route('admin.classrooms.create') }}" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Ruang Kelas
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl p-4 shadow-sm animate-fade-in">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tahun Pelajaran</label>
                    <select name="academic_year_id" id="yearSelect" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Tahun Pelajaran</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>
                                {{ $ay->year }} {{ $ay->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Unit Sekolah</label>
                    <select name="school_id" id="schoolSelect" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" data-type="{{ $school->type }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if($isSMK)
                <!-- SMK Filters: Program Keahlian and Level -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Program Keahlian</label>
                    <select name="program_keahlian_id" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Program</option>
                        @foreach($programKeahlians as $pk)
                            <option value="{{ $pk->id }}" {{ request('program_keahlian_id') == $pk->id ? 'selected' : '' }}>
                                {{ $pk->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Level</label>
                    <select name="grade_level" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Level</option>
                        <option value="10" {{ request('grade_level') == '10' ? 'selected' : '' }}>Level 10</option>
                        <option value="11" {{ request('grade_level') == '11' ? 'selected' : '' }}>Level 11</option>
                        <option value="12" {{ request('grade_level') == '12' ? 'selected' : '' }}>Level 12</option>
                    </select>
                </div>
                @elseif($schoolType === 'SMA')
                <!-- SMA Filters: Level only with Roman numerals -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Level</label>
                    <select name="grade_level" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Level</option>
                        <option value="10" {{ request('grade_level') == '10' ? 'selected' : '' }}>Level X</option>
                        <option value="11" {{ request('grade_level') == '11' ? 'selected' : '' }}>Level XI</option>
                        <option value="12" {{ request('grade_level') == '12' ? 'selected' : '' }}>Level XII</option>
                    </select>
                </div>
                @elseif($schoolType === 'SMP')
                <!-- SMP Filters: Tingkat only -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Level</label>
                    <select name="grade_level" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Level</option>
                        <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Level 7</option>
                        <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Level 8</option>
                        <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Level 9</option>
                    </select>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                    <select name="class_name" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Kelas</option>
                        @foreach($availableClasses as $className)
                            <option value="{{ $className }}" {{ request('class_name') == $className ? 'selected' : '' }}>
                                {{ $className }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex items-center gap-2 mt-4">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-2 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['school_id', 'grade_level', 'class_name', 'program_keahlian_id', 'academic_year_id']))
                <a href="{{ route('admin.classrooms.index') }}" class="flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-xl font-semibold transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>
    
    @if(auth()->user()->isSuperAdmin())
    <script>
        // Auto-submit form when school changes (to reload Program Keahlian options)
        document.getElementById('schoolSelect')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
    @endif

    <script>
        // Auto-submit form when year changes
        document.getElementById('yearSelect')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
    
    <script>
        // Auto-refresh class dropdown when level or program keahlian changes
        const filterForm = document.getElementById('filterForm');
        const gradeLevelSelect = filterForm?.querySelector('select[name="grade_level"]');
        const programKeahlianSelect = filterForm?.querySelector('select[name="program_keahlian_id"]');
        
        // Add change listeners to update available classes
        if (gradeLevelSelect) {
            gradeLevelSelect.addEventListener('change', function() {
                // Auto-submit to refresh class dropdown
                filterForm.submit();
            });
        }
        
        if (programKeahlianSelect) {
            programKeahlianSelect.addEventListener('change', function() {
                // Auto-submit to refresh class dropdown
                filterForm.submit();
            });
        }
    </script>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-white px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Daftar Ruang Kelas
                </h2>
                <div class="bg-gray-100 px-4 py-2 rounded-lg">
                    <span class="text-gray-700 font-semibold text-sm">Total: <span class="text-cyan-600 font-bold text-lg">{{ $classrooms->total() }}</span> Kelas</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">
                            <div class="flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                No
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Nama Kelas
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Foto
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Wali Kelas
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Kapasitas / Siswa
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-48 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Aksi
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($classrooms as $index => $c)
                    @php
                        $avatar = $c->getAvatarConfig();
                    @endphp
                    <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all duration-200 group">
                        <td class="px-4 py-4 text-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow">
                                {{ $classrooms->firstItem() + $index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($avatar['icon'])
                                {{-- Predefined Avatar with unique gradient + custom SVG icon --}}
                                <div class="relative group/avatar">
                                    <div class="w-14 h-14 bg-gradient-to-br {{ $avatar['gradient'] }} rounded-full flex items-center justify-center shadow-lg ring-2 {{ $avatar['ring'] }} group-hover:scale-110 transition-all duration-300 group-hover:shadow-xl">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $avatar['icon'] !!}
                                        </svg>
                                    </div>
                                    {{-- Tooltip --}}
                                    <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded-md whitespace-nowrap opacity-0 group-hover/avatar:opacity-100 transition-opacity pointer-events-none z-10">
                                        {{ $avatar['name'] }}
                                    </div>
                                </div>
                                @else
                                {{-- Beautiful letter icon with dynamic gradient --}}
                                <div class="w-14 h-14 bg-gradient-to-br {{ $avatar['gradient'] }} rounded-xl flex items-center justify-center text-white font-bold text-base shadow-md group-hover:scale-110 transition-transform ring-2 {{ $avatar['ring'] ?? 'ring-white/20' }}">
                                    {{ $avatar['initials'] }}
                                </div>
                                @endif

                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-gray-900 text-base">{{ $c->class_name }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold {{ $c->academicYear->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                            {{ $c->academicYear->year }}
                                        </span>
                                    </div>
                                    @if($avatar['is_predefined'])
                                    <div class="text-xs text-gray-500 mt-0.5 italic">{{ $avatar['name'] }}</div>
                                    @endif
                                    @if(auth()->user()->isSuperAdmin())
                                    <div class="text-xs text-gray-400 mt-0.5">{{ optional($c->school)->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                @if($c->homeroomTeacher)
                                    @if($c->homeroomTeacher->photo)
                                        <img src="{{ asset('storage/' . $c->homeroomTeacher->photo) }}" 
                                             alt="{{ $c->homeroomTeacher->full_name }}"
                                             class="w-14 h-14 rounded-full object-cover border-3 border-white shadow-lg ring-2 ring-indigo-200 group-hover:scale-110 group-hover:ring-indigo-400 transition-all">
                                    @else
                                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-purple-200 group-hover:scale-110 group-hover:ring-purple-400 transition-all">
                                            {{ strtoupper(substr($c->homeroomTeacher->full_name, 0, 2)) }}
                                        </div>
                                    @endif
                                @else
                                    <div class="w-14 h-14 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center text-gray-400 shadow-md">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($c->homeroomTeacher)
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">{{ $c->homeroomTeacher->full_name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                        <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $c->homeroomTeacher->teacher_code }}</span>
                                        @if($c->homeroomTeacher->phone)
                                        <span class="text-gray-400">•</span>
                                        <span>{{ $c->homeroomTeacher->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2 text-gray-400 italic text-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Belum ditentukan</span>
                                    </div>
                                    <a href="{{ route('admin.classrooms.assignHomeroom', $c) }}"
                                       class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tunjuk Wali Kelas
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $studentCount = $c->students_count ?? 0;
                                $capacity = $c->capacity ?? 0;
                                $percentage = $capacity > 0 ? min(($studentCount / $capacity) * 100, 100) : 0;
                                
                                // SVG circle params
                                $radius = 24;
                                $circumference = 2 * M_PI * $radius;
                                $dashOffset = $circumference - ($percentage / 100) * $circumference;

                                // Color based on percentage
                                if ($percentage >= 100) {
                                    $strokeColor = '#ef4444'; // red
                                    $bgRing = '#fecaca';
                                    $textColor = 'text-red-600';
                                    $label = 'Penuh';
                                    $labelColor = 'text-red-500';
                                } elseif ($percentage >= 90) {
                                    $strokeColor = '#f97316'; // orange
                                    $bgRing = '#fed7aa';
                                    $textColor = 'text-orange-600';
                                    $label = 'Hampir Penuh';
                                    $labelColor = 'text-orange-500';
                                } elseif ($percentage >= 75) {
                                    $strokeColor = '#eab308'; // yellow 
                                    $bgRing = '#fef08a';
                                    $textColor = 'text-yellow-600';
                                    $label = 'Cukup';
                                    $labelColor = 'text-yellow-500';
                                } else {
                                    $strokeColor = '#22c55e'; // green
                                    $bgRing = '#bbf7d0';
                                    $textColor = 'text-green-600';
                                    $label = 'Tersedia';
                                    $labelColor = 'text-green-500';
                                }
                            @endphp
                            <div class="flex flex-col items-center gap-1">
                                {{-- SVG Circular Progress Ring --}}
                                <div class="relative w-16 h-16">
                                    <svg class="w-16 h-16 -rotate-90" viewBox="0 0 60 60">
                                        {{-- Background ring --}}
                                        <circle cx="30" cy="30" r="{{ $radius }}" fill="none" stroke="{{ $bgRing }}" stroke-width="5"/>
                                        {{-- Progress arc --}}
                                        <circle cx="30" cy="30" r="{{ $radius }}" fill="none" stroke="{{ $strokeColor }}" stroke-width="5"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $dashOffset }}"
                                            class="transition-all duration-700 ease-out"/>
                                    </svg>
                                    {{-- Center text --}}
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="font-bold text-sm {{ $textColor }}">{{ number_format($percentage, 0) }}%</span>
                                    </div>
                                </div>
                                {{-- Student count label --}}
                                <div class="text-center">
                                    <div class="font-semibold text-xs text-gray-700">{{ $studentCount }}<span class="text-gray-400">/</span>{{ $capacity }}</div>
                                    <div class="text-xs font-medium {{ $labelColor }}">{{ $label }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.classrooms.show', $c) }}" 
                                    class="group/btn relative p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-all transform hover:scale-110 hover:shadow-lg" 
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.classrooms.edit', $c) }}" 
                                    class="p-2.5 bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all transform hover:scale-110 hover:shadow-lg" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.classrooms.assignStudents', $c) }}" 
                                    class="p-2.5 bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg transition-all transform hover:scale-110 hover:shadow-lg" 
                                    title="Kelola Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.classrooms.destroy', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="p-2.5 bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all transform hover:scale-110 hover:shadow-lg" 
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus kelas {{ $c->class_name }}? Semua data siswa di kelas ini akan terpengaruh!')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-semibold text-lg mb-1">Belum ada ruang kelas</p>
                                    <p class="text-gray-500 text-sm mb-4">Mulai dengan menambahkan kelas pertama Anda</p>
                                </div>
                                <a href="{{ route('admin.classrooms.create') }}" 
                                   class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Tambah Kelas Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classrooms->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $classrooms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection