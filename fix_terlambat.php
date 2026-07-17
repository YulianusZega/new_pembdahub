<?php
$content = file_get_contents("resources/views/admin/employees/attendance/monitoring.blade.php");

$content = str_replace("\$dailyStats['terlambat']", "\$dailyStats['dinas_luar']", $content);
$content = str_replace("\$cumulativeStats['terlambat']", "\$cumulativeStats['dinas_luar']", $content);
$content = str_replace("cumTerlambat", "cumDinasLuar", $content);
$content = str_replace("cs.terlambat", "cs.dinas_luar", $content);
$content = str_replace("live_cum_terlambat", "live_cum_dinas_luar", $content);
$content = str_replace("'Terlambat'", "'Dinas Luar'", $content);
$content = str_replace("\$cumulativeStats['pulang']", "\$cumulativeStats['cuti']", $content);
$content = str_replace("\$dailyStats['pulang']", "\$dailyStats['cuti']", $content);
$content = str_replace("item.tipe === 'pulang' ? 'bg-purple-50", "item.tipe === 'cuti' ? 'bg-teal-50 text-teal-700 border-teal-100' : (item.tipe === 'dinas_luar' ? 'bg-purple-50", $content);

file_put_contents("resources/views/admin/employees/attendance/monitoring.blade.php", $content);
?>
