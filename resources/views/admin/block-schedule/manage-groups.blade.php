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

    .dropzone { min-height: 200px; border-radius: 12px; padding: 12px; transition: all 0.2s; }
    .dropzone-unassigned { background: #f8fafc; border: 2px dashed #cbd5e1; }
    .dropzone-a { background: #eff6ff; border: 2px dashed #93c5fd; }
    .dropzone-b { background: #fff7ed; border: 2px dashed #fdba74; }
    
    .dropzone.drag-over { border-style: solid; background-color: rgba(255,255,255,0.8); }
    .dropzone-a.drag-over { border-color: #3b82f6; }
    .dropzone-b.drag-over { border-color: #f97316; }

    .student-card { background: white; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; cursor: grab; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; transition: all 0.2s; user-select: none; }
    .student-card:active { cursor: grabbing; transform: scale(0.98); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .student-card:hover { border-color: #e2e8f0; }
    
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #64748b; flex-shrink: 0; }
    
    .student-info { flex: 1; min-width: 0; }
    .student-name { font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .student-nis { font-size: 11px; color: #64748b; }
    
    .drag-handle { color: #cbd5e1; cursor: grab; }

    .panel { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); display: flex; flex-direction: column; height: 100%; }
    .panel-header { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
    .panel-header-a { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; }
    .panel-header-b { background: linear-gradient(135deg, #9a3412, #f97316); color: white; }
    .panel-header-u { background: #f1f5f9; color: #334155; border-bottom: 1px solid #e2e8f0; }
    
    .count-badge { background: rgba(255,255,255,0.2); padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; backdrop-filter: blur(4px); }
    .panel-header-u .count-badge { background: #e2e8f0; color: #475569; }
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
        $groupAStudents = $studentClasses->filter(fn($sc) => isset($existingGroups[$sc->student_id]) && $existingGroups[$sc->student_id] === 'A');
        $groupBStudents = $studentClasses->filter(fn($sc) => isset($existingGroups[$sc->student_id]) && $existingGroups[$sc->student_id] === 'B');
        $unassignedStudents = $studentClasses->filter(fn($sc) => !isset($existingGroups[$sc->student_id]));
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-gray-100 text-gray-600"><i class="fas fa-users"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Total Siswa</div>
                <div class="text-xl font-black text-gray-800" id="stat-total">{{ $totalStudents }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-users-viewfinder"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Grup A</div>
                <div class="text-xl font-black text-blue-700" id="stat-a">0</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-book-reader"></i></div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase">Grup B</div>
                <div class="text-xl font-black text-orange-700" id="stat-b">0</div>
            </div>
        </div>
        <div class="stat-card" id="stat-card-u">
            <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-user-clock"></i></div>
            <div>
                <div class="text-xs font-bold text-red-500 uppercase" id="label-u">Belum Dibagi</div>
                <div class="text-xl font-black text-red-700" id="stat-u">0</div>
            </div>
        </div>
    </div>

    <!-- Actions & Form -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Metode Pembagian</h3>
            <p class="text-xs text-gray-500">Tarik nama siswa (Drag & Drop) atau gunakan tombol otomatis.</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.block-schedule.groups.auto', $classroom->id) }}" method="POST" class="inline" id="autoAssignForm">
                @csrf
                <button type="button" class="btn-action btn-auto" onclick="confirmAutoAssign()">
                    <i class="fas fa-magic"></i> Bagi Otomatis (50:50)
                </button>
            </form>
            
            <form action="{{ route('admin.block-schedule.groups.save', $classroom->id) }}" method="POST" id="saveGroupsForm">
                @csrf
                <div id="hiddenInputsContainer"></div>
                <button type="submit" class="btn-action btn-save">
                    <i class="fas fa-save"></i> Simpan Pembagian
                </button>
            </form>
        </div>
    </div>

    <!-- Unassigned Pool -->
    <div class="panel mb-6 border border-gray-200">
        <div class="panel-header panel-header-u">
            <h3 class="font-bold"><i class="fas fa-users mr-2"></i> Belum Dibagi</h3>
            <div class="count-badge" id="badge-u">0</div>
        </div>
        <div class="p-4 bg-gray-50">
            <div class="dropzone dropzone-unassigned flex flex-wrap gap-2" id="pool-u" data-group="u">
                @foreach($unassignedStudents as $sc)
                    @if($sc->student)
                    <div class="student-card w-full sm:w-[calc(50%-0.5rem)] md:w-[calc(33.33%-0.5rem)] lg:w-[calc(25%-0.5rem)]" draggable="true" data-id="{{ $sc->student_id }}">
                        <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                        <div class="avatar">{{ strtoupper(substr($sc->student->name ?? '', 0, 1)) }}</div>
                        <div class="student-info">
                            <div class="student-name" title="{{ $sc->student->name }}">{{ $sc->student->name }}</div>
                            <div class="student-nis">{{ $sc->student->nis ?? $sc->student->nisn ?? '-' }}</div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Group Panels A & B -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Group A -->
        <div class="panel border border-blue-200">
            <div class="panel-header panel-header-a">
                <h3 class="font-bold"><i class="fas fa-users-viewfinder mr-2"></i> GRUP A</h3>
                <div class="count-badge" id="badge-a">0</div>
            </div>
            <div class="p-4 bg-blue-50/30 flex-1">
                <div class="dropzone dropzone-a h-full" id="pool-a" data-group="A">
                    @foreach($groupAStudents as $sc)
                        @if($sc->student)
                        <div class="student-card" draggable="true" data-id="{{ $sc->student_id }}">
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <div class="avatar">{{ strtoupper(substr($sc->student->name ?? '', 0, 1)) }}</div>
                            <div class="student-info">
                                <div class="student-name" title="{{ $sc->student->name }}">{{ $sc->student->name }}</div>
                                <div class="student-nis">{{ $sc->student->nis ?? $sc->student->nisn ?? '-' }}</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Group B -->
        <div class="panel border border-orange-200">
            <div class="panel-header panel-header-b">
                <h3 class="font-bold"><i class="fas fa-book-reader mr-2"></i> GRUP B</h3>
                <div class="count-badge" id="badge-b">0</div>
            </div>
            <div class="p-4 bg-orange-50/30 flex-1">
                <div class="dropzone dropzone-b h-full" id="pool-b" data-group="B">
                    @foreach($groupBStudents as $sc)
                        @if($sc->student)
                        <div class="student-card" draggable="true" data-id="{{ $sc->student_id }}">
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <div class="avatar">{{ strtoupper(substr($sc->student->name ?? '', 0, 1)) }}</div>
                            <div class="student-info">
                                <div class="student-name" title="{{ $sc->student->name }}">{{ $sc->student->name }}</div>
                                <div class="student-nis">{{ $sc->student->nis ?? $sc->student->nisn ?? '-' }}</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropzones = document.querySelectorAll('.dropzone');
        let draggedItem = null;

        // Attach drag events to ALL student cards (including initial ones)
        function initDraggable(card) {
            card.addEventListener('dragstart', function() {
                draggedItem = this;
                setTimeout(() => this.style.opacity = '0.5', 0);
            });
            card.addEventListener('dragend', function() {
                setTimeout(() => {
                    this.style.opacity = '1';
                    draggedItem = null;
                }, 0);
                updateCounts();
            });
        }

        document.querySelectorAll('.student-card').forEach(card => initDraggable(card));

        // Dropzone events
        dropzones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            zone.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (draggedItem) {
                    if (this.id === 'pool-u') {
                        draggedItem.className = 'student-card w-full sm:w-[calc(50%-0.5rem)] md:w-[calc(33.33%-0.5rem)] lg:w-[calc(25%-0.5rem)]';
                    } else {
                        draggedItem.className = 'student-card';
                    }
                    draggedItem.setAttribute('draggable', 'true');
                    this.appendChild(draggedItem);
                    updateCounts();
                }
            });
        });

        // Save form handler — inject hidden inputs before submit
        document.getElementById('saveGroupsForm').addEventListener('submit', function(e) {
            const container = document.getElementById('hiddenInputsContainer');
            container.innerHTML = '';

            const cardsA = document.querySelectorAll('#pool-a .student-card');
            const cardsB = document.querySelectorAll('#pool-b .student-card');

            if (cardsA.length === 0 && cardsB.length === 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Perhatian', 'Belum ada siswa yang dibagi ke Grup A atau B.', 'warning');
                } else {
                    alert('Belum ada siswa yang dibagi ke Grup A atau B.');
                }
                return;
            }

            cardsA.forEach(card => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `groups[${card.dataset.id}]`;
                input.value = 'A';
                container.appendChild(input);
            });

            cardsB.forEach(card => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `groups[${card.dataset.id}]`;
                input.value = 'B';
                container.appendChild(input);
            });
        });

        function updateCounts() {
            const countA = document.getElementById('pool-a').querySelectorAll('.student-card').length;
            const countB = document.getElementById('pool-b').querySelectorAll('.student-card').length;
            const countU = document.getElementById('pool-u').querySelectorAll('.student-card').length;

            document.getElementById('stat-a').innerText = countA;
            document.getElementById('stat-b').innerText = countB;
            document.getElementById('stat-u').innerText = countU;
            
            document.getElementById('badge-a').innerText = countA;
            document.getElementById('badge-b').innerText = countB;
            document.getElementById('badge-u').innerText = countU;

            const statCardU = document.getElementById('stat-card-u');
            if (countU === 0) {
                statCardU.style.borderColor = '#a7f3d0';
                statCardU.style.background = 'rgba(236,253,245,0.3)';
                document.getElementById('label-u').innerText = 'Selesai ✓';
                document.getElementById('label-u').className = 'text-xs font-bold text-emerald-600 uppercase';
                document.getElementById('stat-u').className = 'text-xl font-black text-emerald-700';
            } else {
                statCardU.style.borderColor = '';
                statCardU.style.background = '';
                document.getElementById('label-u').innerText = 'Belum Dibagi';
                document.getElementById('label-u').className = 'text-xs font-bold text-red-500 uppercase';
                document.getElementById('stat-u').className = 'text-xl font-black text-red-700';
            }
        }

        updateCounts();
    });

    function confirmAutoAssign() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Bagi Otomatis (50:50)',
                text: 'Siswa akan dibagi berdasarkan urutan nama. Pembagian yang sudah ada akan ditimpa.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8b5cf6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Bagi Otomatis',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('autoAssignForm').submit();
                }
            });
        } else {
            if (confirm('Siswa akan dibagi berdasarkan urutan nama. Pembagian yang sudah ada akan ditimpa. Lanjutkan?')) {
                document.getElementById('autoAssignForm').submit();
            }
        }
    }
</script>
@endsection
