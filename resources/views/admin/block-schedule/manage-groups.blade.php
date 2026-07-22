@extends('layouts.admin')

@section('title', 'Pembagian Grup Blok - ' . $classroom->class_name)

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .group-page { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #fff7ed 0%, #fffbeb 50%, #fef3c7 100%); padding: 24px 24px 8px; min-height: 100vh; }
    
    .page-header { background: linear-gradient(135deg, #f97316 0%, #ea580c 40%, #d97706 80%, #b45309 100%); border-radius: 20px; padding: 24px 32px; margin-bottom: 24px; color: white; box-shadow: 0 10px 30px -10px rgba(234,88,12,0.4); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    
    .stat-card { background: white; border-radius: 16px; padding: 16px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    
    .btn-action { padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-save { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16,185,129,0.4); }
    .btn-auto { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
    .btn-auto:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(124,58,237,0.4); }
    .btn-back { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px); text-decoration: none; }
    .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }

    .student-row { background: white; border-radius: 12px; padding: 12px 16px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; transition: all 0.2s; gap: 16px; flex-wrap: wrap; }
    .student-row:hover { border-color: #e2e8f0; background: #f8fafc; }
    
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; color: #64748b; flex-shrink: 0; }
    
    .student-info { flex: 1; min-width: 0; }
    .student-name { font-size: 14px; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .student-nis { font-size: 12px; color: #64748b; margin-top: 2px; }

    .checkbox-group { display: flex; align-items: center; gap: 20px; background: #f8fafc; padding: 8px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-left: auto; }
    .checkbox-group label { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: color 0.2s; }
    .chk-a-label { color: #1e40af; }
    .chk-b-label { color: #9a3412; }
    .chk-a-label:hover { color: #1d4ed8; }
    .chk-b-label:hover { color: #c2410c; }
    
    .custom-checkbox { width: 20px; height: 20px; border-radius: 6px; border: 2px solid #cbd5e1; cursor: pointer; accent-color: currentColor; }

    .panel { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .panel-header { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    
    .count-badge { background: #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 800; }
</style>

<div class="group-page">
    @if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0"><i class="fas fa-check text-emerald-600"></i></div>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0"><i class="fas fa-times text-red-600"></i></div>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <div class="page-header">
        <div>
            <div class="flex items-center gap-2 text-sm text-orange-200 mb-2 font-medium">
                <a href="{{ route('admin.block-schedule.index') }}" class="hover:text-white transition-colors">Sistem Blok</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span>Pembagian Grup</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Kelas {{ $classroom->class_name }}</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.block-schedule.index') }}" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    @php
        $totalStudents = $studentClasses->count();
        $groupACount = 0;
        $groupBCount = 0;
        foreach($existingGroups as $grp) {
            if ($grp === 'A') $groupACount++;
            if ($grp === 'B') $groupBCount++;
        }
        $unassignedCount = $totalStudents - $groupACount - $groupBCount;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-gray-100 text-gray-600"><i class="fas fa-users"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Total Siswa</div>
                <div class="text-xl font-black text-gray-800">{{ $totalStudents }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-users-viewfinder"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Grup A</div>
                <div class="text-xl font-black text-blue-700" id="stat-a">{{ $groupACount }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-book-reader"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Grup B</div>
                <div class="text-xl font-black text-orange-700" id="stat-b">{{ $groupBCount }}</div>
            </div>
        </div>
        <div class="stat-card" id="stat-card-u" style="{{ $unassignedCount == 0 ? 'border-color: #a7f3d0; background: rgba(236,253,245,0.3);' : '' }}">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-user-clock"></i></div>
            <div>
                <div class="text-xs font-bold uppercase {{ $unassignedCount == 0 ? 'text-emerald-600' : 'text-red-500' }}" id="label-u">
                    {{ $unassignedCount == 0 ? 'Selesai ✓' : 'Belum Dibagi' }}
                </div>
                <div class="text-xl font-black {{ $unassignedCount == 0 ? 'text-emerald-700' : 'text-red-700' }}" id="stat-u">{{ $unassignedCount }}</div>
            </div>
        </div>
    </div>

    <!-- Actions & Form -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Daftar Siswa</h3>
            <p class="text-xs text-gray-500">Centang kotak Group A atau Group B pada masing-masing siswa.</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.block-schedule.groups.auto', $classroom->id) }}" method="POST" class="inline" id="autoAssignForm">
                @csrf
                <button type="button" class="btn-action btn-auto" onclick="confirmAutoAssign()">
                    <i class="fas fa-magic"></i> Bagi Otomatis (50:50)
                </button>
            </form>
            
            <button type="button" class="btn-action btn-save" onclick="document.getElementById('saveGroupsForm').submit()">
                <i class="fas fa-save"></i> Simpan Pembagian
            </button>
        </div>
    </div>

    <!-- Student List Panel -->
    <div class="panel mb-6">
        <form action="{{ route('admin.block-schedule.groups.save', $classroom->id) }}" method="POST" id="saveGroupsForm">
            @csrf
            
            <div class="p-4 bg-gray-50/50">
                @foreach($studentClasses as $sc)
                    @if($sc->student)
                    @php
                        $group = $existingGroups[$sc->student_id] ?? null;
                    @endphp
                    <div class="student-row">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="avatar">{{ strtoupper(substr($sc->student->full_name ?? '', 0, 1)) }}</div>
                            <div class="student-info">
                                <div class="student-name" title="{{ $sc->student->full_name }}">{{ $sc->student->full_name }}</div>
                                <div class="student-nis">{{ $sc->student->nis ?? $sc->student->nisn ?? '-' }}</div>
                            </div>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="chk-a-label">
                                Group A 
                                <input type="checkbox" name="groups[{{ $sc->student_id }}]" value="A" class="custom-checkbox chk-a" data-id="{{ $sc->student_id }}" onchange="toggleCheck(this)" {{ $group === 'A' ? 'checked' : '' }}>
                            </label>
                            <span class="text-gray-300">|</span>
                            <label class="chk-b-label">
                                Group B 
                                <input type="checkbox" name="groups[{{ $sc->student_id }}]" value="B" class="custom-checkbox chk-b" data-id="{{ $sc->student_id }}" onchange="toggleCheck(this)" {{ $group === 'B' ? 'checked' : '' }}>
                            </label>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAutoAssign() {
        Swal.fire({
            title: 'Bagi Otomatis (50:50)?',
            text: "Ini akan mengatur ulang semua grup A dan B berdasarkan urutan nama. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Bagi Otomatis',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('autoAssignForm').submit();
            }
        });
    }

    // Toggle behavior: Ensure only one checkbox is checked per student
    window.toggleCheck = function(checkbox) {
        const groupContainer = checkbox.closest('.checkbox-group');
        const otherCheckbox = groupContainer.querySelector('input[type="checkbox"]:not([value="' + checkbox.value + '"])');
        
        if (checkbox.checked) {
            otherCheckbox.checked = false;
        }
        
        updateCounts();
    };

    function updateCounts() {
        let countA = 0;
        let countB = 0;
        const total = {{ $totalStudents }};
        
        document.querySelectorAll('.chk-a').forEach(chk => {
            if (chk.checked) countA++;
        });
        document.querySelectorAll('.chk-b').forEach(chk => {
            if (chk.checked) countB++;
        });
        
        let countU = total - countA - countB;
        if (countU < 0) countU = 0;
        
        document.getElementById('stat-a').innerText = countA;
        document.getElementById('stat-b').innerText = countB;
        document.getElementById('stat-u').innerText = countU;
        
        const statCardU = document.getElementById('stat-card-u');
        if (countU === 0) {
            statCardU.style.borderColor = '#a7f3d0';
            statCardU.style.background = 'rgba(236,253,245,0.3)';
            document.getElementById('label-u').innerText = 'Selesai ✓';
            document.getElementById('label-u').className = 'text-xs font-bold text-emerald-600 uppercase';
            document.getElementById('stat-u').className = 'text-xl font-black text-emerald-700';
        } else {
            statCardU.style.borderColor = 'rgba(0,0,0,0.05)';
            statCardU.style.background = 'white';
            document.getElementById('label-u').innerText = 'Belum Dibagi';
            document.getElementById('label-u').className = 'text-xs font-bold text-red-500 uppercase';
            document.getElementById('stat-u').className = 'text-xl font-black text-red-700';
        }
    }
    
    // Prevent form submission if no student is selected
    document.getElementById('saveGroupsForm').addEventListener('submit', function(e) {
        let hasAnyChecked = false;
        document.querySelectorAll('.custom-checkbox').forEach(chk => {
            if (chk.checked) hasAnyChecked = true;
        });
        
        if (!hasAnyChecked) {
            e.preventDefault();
            Swal.fire('Perhatian', 'Belum ada siswa yang dibagi ke Grup A atau B.', 'warning');
        }
    });
</script>
@endsection
