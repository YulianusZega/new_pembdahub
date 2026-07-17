<?php
$file = "app/Http/Controllers/Admin/EmployeeAttendanceController.php";
$content = file_get_contents($file);

$oldCode = <<<EOT
        // Calculate 'Z' -> working days since start of month
        \$workingDays = 0;
        for (\$d = \$startDateOfMonth->copy(); \$d->lte(\$endDate); \$d->addDay()) {
            if (!in_array(\$d->dayOfWeek, [0, 6])) { // Skip Sun, Sat
                \$workingDays++;
            }
        }
        
        \$cumulativeStats = [
            'hadir' => (clone \$cumulativeStatsQuery)->where('status', 'hadir')->count(),
            'sakit' => (clone \$cumulativeStatsQuery)->where('status', 'sakit')->count(),
            'izin' => (clone \$cumulativeStatsQuery)->where('status', 'izin')->count(),
            'alpha' => (clone \$cumulativeStatsQuery)->where('status', 'alpha')->count(),
            'dinas_luar' => (clone \$cumulativeStatsQuery)->where('status', 'dinas_luar')->count(),
            'cuti' => (clone \$cumulativeStatsQuery)->where('status', 'cuti')->count(),
            'z' => \$workingDays,
            'active_employees' => \$activeEmployeeCount
        ];
EOT;

$newCode = <<<EOT
        // Calculate 'Z' -> working days since start of month
        \$workingDays = 0;
        for (\$d = \$startDateOfMonth->copy(); \$d->lte(\$endDate); \$d->addDay()) {
            if (!in_array(\$d->dayOfWeek, [0, 6])) { // Skip Sun, Sat
                \$workingDays++;
            }
        }
        
        // Calculate dynamic expected Z (total expected attendances) for all active employees
        \$totalExpectedAttendances = 0;
        \$allActiveEmployees = \App\Models\Employee::where('is_active', true)
            ->when(\$schoolId, function(\$q) use (\$schoolId) {
                return \$q->where('school_id', \$schoolId);
            })
            ->with('teacher.schedules')
            ->get();
            
        foreach (\$allActiveEmployees as \$emp) {
            \$isTeacher = \$emp->teacher !== null && \$emp->teacher->schedules->count() > 0;
            \$teachingDays = [];
            if (\$isTeacher) {
                \$teachingDays = \$emp->teacher->schedules->pluck('day_of_week')->map(function(\$day) {
                    return strtolower(\$day);
                })->unique()->toArray();
            }
            
            for (\$d = \$startDateOfMonth->copy(); \$d->lte(\$endDate); \$d->addDay()) {
                if (\$isTeacher) {
                    if (in_array(strtolower(\$d->format('l')), \$teachingDays)) {
                        \$totalExpectedAttendances++;
                    }
                } else {
                    if (!in_array(\$d->dayOfWeek, [0, 6])) {
                        \$totalExpectedAttendances++;
                    }
                }
            }
        }
        
        \$cumulativeStats = [
            'hadir' => (clone \$cumulativeStatsQuery)->where('status', 'hadir')->count(),
            'sakit' => (clone \$cumulativeStatsQuery)->where('status', 'sakit')->count(),
            'izin' => (clone \$cumulativeStatsQuery)->where('status', 'izin')->count(),
            'alpha' => (clone \$cumulativeStatsQuery)->where('status', 'alpha')->count(),
            'dinas_luar' => (clone \$cumulativeStatsQuery)->where('status', 'dinas_luar')->count(),
            'cuti' => (clone \$cumulativeStatsQuery)->where('status', 'cuti')->count(),
            'z' => \$workingDays,
            'expected_attendances' => \$totalExpectedAttendances,
            'active_employees' => \$activeEmployeeCount
        ];
EOT;

$content = str_replace($oldCode, $newCode, $content);

$oldCode2 = <<<EOT
        foreach (\$schools as \$sch) {
            \$empCount = Employee::where('school_id', \$sch->id)->where('is_active', true)->count();
            \$schDaily = (clone \$dailyStatsQuery)->whereHas('employee', fn(\$q) => \$q->where('school_id', \$sch->id))->get();
            \$schHadir = \$schDaily->where('status', 'hadir')->count();
            \$schCumHadir = (clone \$cumulativeStatsQuery)->whereHas('employee', fn(\$q) => \$q->where('school_id', \$sch->id))->where('status', 'hadir')->count();
            
            \$presenceRate = (\$workingDays > 0 && \$empCount > 0) ? round((\$schCumHadir / (\$workingDays * \$empCount)) * 100, 1) : 0;
            
            \$unitStats[] = (object) [
                'school_id' => \$sch->id,
                'school_name' => \$sch->name,
                'school' => (object) ['name' => \$sch->name],
                'employees_count' => \$empCount,
                'total_hadir' => \$schCumHadir,
                'daily_present' => \$schHadir,
                'z_days' => \$workingDays,
                'presence_rate' => \$presenceRate
            ];
        }
EOT;

$newCode2 = <<<EOT
        foreach (\$schools as \$sch) {
            \$empCount = Employee::where('school_id', \$sch->id)->where('is_active', true)->count();
            
            // Calculate expected attendances specifically for this school
            \$schExpectedAttendances = 0;
            \$schEmployees = \$allActiveEmployees->where('school_id', \$sch->id);
            foreach (\$schEmployees as \$emp) {
                \$isTeacher = \$emp->teacher !== null && \$emp->teacher->schedules->count() > 0;
                \$teachingDays = [];
                if (\$isTeacher) {
                    \$teachingDays = \$emp->teacher->schedules->pluck('day_of_week')->map(function(\$day) {
                        return strtolower(\$day);
                    })->unique()->toArray();
                }
                for (\$d = \$startDateOfMonth->copy(); \$d->lte(\$endDate); \$d->addDay()) {
                    if (\$isTeacher) {
                        if (in_array(strtolower(\$d->format('l')), \$teachingDays)) {
                            \$schExpectedAttendances++;
                        }
                    } else {
                        if (!in_array(\$d->dayOfWeek, [0, 6])) {
                            \$schExpectedAttendances++;
                        }
                    }
                }
            }
            
            \$schDaily = (clone \$dailyStatsQuery)->whereHas('employee', fn(\$q) => \$q->where('school_id', \$sch->id))->get();
            \$schHadir = \$schDaily->where('status', 'hadir')->count();
            \$schCumHadir = (clone \$cumulativeStatsQuery)->whereHas('employee', fn(\$q) => \$q->where('school_id', \$sch->id))->where('status', 'hadir')->count();
            
            \$presenceRate = \$schExpectedAttendances > 0 ? round((\$schCumHadir / \$schExpectedAttendances) * 100, 1) : 0;
            
            \$unitStats[] = (object) [
                'school_id' => \$sch->id,
                'school_name' => \$sch->name,
                'school' => (object) ['name' => \$sch->name],
                'employees_count' => \$empCount,
                'total_hadir' => \$schCumHadir,
                'daily_present' => \$schHadir,
                'z_days' => \$workingDays,
                'expected_attendances' => \$schExpectedAttendances,
                'presence_rate' => \$presenceRate
            ];
        }
EOT;

$content = str_replace($oldCode2, $newCode2, $content);
file_put_contents($file, $content);
?>
