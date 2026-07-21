@extends('layouts.admin')

@section('title', 'Jadwal Blok Kelas')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .schedule-page { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 24px 24px 8px; min-height: 100vh; }
    
    .page-header { background: linear-gradient(135deg, #f97316 0%, #ea580c 40%, #d97706 80%, #b45309 100%); border-radius: 20px; padding: 24px 32px; margin-bottom: 24px; color: white; box-shadow: 0 10px 30px -10px rgba(234,88,12,0.4); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; }
    
    .filter-select { width: 100%; padding: 10px 16px; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; color: #1e293b; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(4px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: pointer; outline: none; transition: all 0.2s; }
    .filter-select:focus { background-color: white; box-shadow: 0 0 0 4px rgba(255,255,255,0.3); }

    .btn-back-sm { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px); padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; text-decoration: none; }
    .btn-back-sm:hover { background: rgba(255,255,255,0.3); color: white; }
    
    .info-bar { background: white; border-radius: 16px; padding: 16px 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px; border-left: 6px solid #f97316; flex-wrap: wrap; }
    .info-icon { width: 48px; height: 48px; border-radius: 12px; background: #fff7ed; color: #f97316; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    
    .grid-panel { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); display: flex; flex-direction: column; height: 100%; border: 1px solid rgba(0,0,0,0.05); }
    .panel-header-a { padding: 16px 20px; text-align: center; font-weight: 800; font-size: 16px; letter-spacing: 0.05em; text-transform: uppercase; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; }
    .panel-header-b { padding: 16px 20px; text-align: center; font-weight: 800; font-size: 16px; letter-spacing: 0.05em; text-transform: uppercase; background: linear-gradient(135deg, #9a3412, #f97316); color: white; }

    .table-container { overflow-x: auto; flex: 1; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th.day-header { background: #f8fafc; padding: 12px 8px; font-size: 12px; font-weight: 800; text-align: center; border-bottom: 2px solid #e2e8f0; border-right: 1px solid #f1f5f9; color: #475569; text-transform: uppercase; }
    th.time-header { background: #f1f5f9; padding: 12px 8px; font-size: 11px; font-weight: 700; text-align: center; border-bottom: 2px solid #e2e8f0; border-right: 2px solid #e2e8f0; color: #475569; width: 60px; position: sticky; left: 0; z-index: 10; }
    
    td.time-cell { background: #f8fafc; padding: 8px; font-size: 11px; font-weight: 700; text-align: center; border-bottom: 1px solid #f1f5f9; border-right: 2px solid #e2e8f0; color: #64748b; position: sticky; left: 0; z-index: 10; }
    td.schedule-cell { padding: 4px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; vertical-align: top; height: 60px; min-width: 120px; }
    
    .schedule-card { padding: 6px; border-radius: 8px; height: 100%; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
    .schedule-card-a { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
    .schedule-card-b { background: linear-gradient(135deg, #fff7ed, #ffedd5); border-color: #fed7aa; }
    
    .subject-name { font-size: 11px; font-weight: 800; line-height: 1.2; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .teacher-name { font-size: 10px; font-weight: 600; color: #64748b; background: rgba(255,255,255,0.7); padding: 2px 4px; border-radius: 4px; display: inline-block; }
    
    .card-a .subject-name { color: #1e3a8a; }
    .card-a .teacher-name { color: #2563eb; }
    .card-b .subject-name { color: #9a3412; }
    .card-b .teacher-name { color: #ea580c; }

    .legend-box { display: inline-flex; align-items: center; gap: 8px; margin-right: 16px; font-size: 12px; font-weight: 600; color: #475569; }
    .legend-color { width: 16px; height: 16px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1); }
</style>

<div class="schedule-page">
    <div class="page-header">
        <div>
            <div class="flex items-center gap-2 text-sm text-orange-200 mb-2 font-medium">
                <a href="{{ route('admin.block-schedule.index') }}" class="hover:text-white transition-colors">Sistem Blok</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span>Lihat Jadwal</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Jadwal Pelajaran Blok</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.block-schedule.view') }}" class="flex items-center gap-3" id="classroomForm">
                <div class="w-64">
                    <select name="classroom_id" class="filter-select" onchange="document.getElementById('classroomForm').submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>
                                {{ $cls->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <a href="{{ route('admin.block-schedule.index') }}" class="btn-back-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($selectedClassroomId)
        <div class="info-bar">
            <div class="info-icon">
                <i class="fas fa-sync-alt {{ $currentRotation == 'swapped' ? 'fa-spin' : '' }}"></i>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status Rotasi Minggu Ini</h3>
                @if($currentRotation == 'swapped')
                    <div class="text-lg font-black text-gray-800">
                        <span class="text-blue-600">Grup A <i class="fas fa-arrow-right text-sm"></i> Teori/Umum</span>
                        <span class="mx-3 text-gray-300">|</span>
                        <span class="text-orange-600">Grup B <i class="fas fa-arrow-right text-sm"></i> Praktik/Kejuruan</span>
                    </div>
                @else
                    <div class="text-lg font-black text-gray-800">
                        <span class="text-blue-600">Grup A <i class="fas fa-arrow-right text-sm"></i> Praktik/Kejuruan</span>
                        <span class="mx-3 text-gray-300">|</span>
                        <span class="text-orange-600">Grup B <i class="fas fa-arrow-right text-sm"></i> Teori/Umum</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white px-4 py-3 rounded-xl shadow-sm mb-4 border border-gray-100 flex items-center gap-4 flex-wrap">
            <span class="text-xs font-bold text-gray-500 uppercase mr-2"><i class="fas fa-info-circle"></i> Keterangan:</span>
            <div class="legend-box">
                <div class="legend-color bg-blue-100 border-blue-200"></div> Kelompok A (Semua Siswa / Umum)
            </div>
            <div class="legend-box">
                <div class="legend-color bg-orange-100 border-orange-200"></div> Kelompok B (Split Grup / Kejuruan)
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Group A Panel -->
            <div class="grid-panel">
                <div class="panel-header-a">
                    <i class="fas fa-users mr-2"></i> KELOMPOK A — Semua Siswa
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="time-header">WAKTU</th>
                                @foreach($dayLabels as $dayKey => $dayLabel)
                                    <th class="day-header">{{ $dayLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="time-cell">
                                        <div class="text-blue-800">{{ $slot->slot_name ?? 'Jam '.$slot->slot_order }}</div>
                                        <div class="text-[9px] text-gray-500">{{ substr($slot->start_time,0,5) }}-{{ substr($slot->end_time,0,5) }}</div>
                                    </td>
                                    @foreach($days as $day)
                                        @php
                                            $item = $scheduleA->first(function($s) use ($day, $slot) {
                                                return strtolower($s->day_of_week ?? '') == $day && $s->time_slot_id == $slot->id;
                                            });
                                        @endphp
                                        <td class="schedule-cell">
                                            @if($item && $item->teachingAssignment)
                                                <div class="schedule-card schedule-card-a card-a">
                                                    <div class="subject-name">{{ $item->teachingAssignment->subject->name ?? '-' }}</div>
                                                    <div>
                                                        <span class="teacher-name">{{ Str::limit($item->teachingAssignment->teacher->full_name ?? '-', 15) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Group B Panel -->
            <div class="grid-panel">
                <div class="panel-header-b">
                    <i class="fas fa-users-cog mr-2"></i> KELOMPOK B — Split Grup (Kejuruan)
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="time-header">WAKTU</th>
                                @foreach($dayLabels as $dayKey => $dayLabel)
                                    <th class="day-header">{{ $dayLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="time-cell">
                                        <div class="text-orange-800">{{ $slot->slot_name ?? 'Jam '.$slot->slot_order }}</div>
                                        <div class="text-[9px] text-gray-500">{{ substr($slot->start_time,0,5) }}-{{ substr($slot->end_time,0,5) }}</div>
                                    </td>
                                    @foreach($days as $day)
                                        @php
                                            $item = $scheduleB->first(function($s) use ($day, $slot) {
                                                return strtolower($s->day_of_week ?? '') == $day && $s->time_slot_id == $slot->id;
                                            });
                                        @endphp
                                        <td class="schedule-cell">
                                            @if($item && $item->teachingAssignment)
                                                <div class="schedule-card schedule-card-b card-b">
                                                    <div class="subject-name">{{ $item->teachingAssignment->subject->name ?? '-' }}</div>
                                                    <div>
                                                        <span class="teacher-name">{{ Str::limit($item->teachingAssignment->teacher->full_name ?? '-', 15) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100 flex flex-col items-center">
            <div class="w-24 h-24 mb-6 rounded-full bg-gray-50 flex items-center justify-center">
                <i class="fas fa-hand-pointer text-gray-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Pilih Kelas Terlebih Dahulu</h3>
            <p class="text-gray-500 max-w-md mx-auto text-sm">
                Silakan gunakan dropdown di bagian atas halaman untuk memilih kelas dan melihat jadwal blok.
            </p>
        </div>
    @endif
</div>
@endsection
