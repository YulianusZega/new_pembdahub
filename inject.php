<?php
$file = 'resources/views/admin/performance_contracts/show.blade.php';
$lines = file($file);
$stepper = file_get_contents('progress_stepper.txt');
array_splice($lines, 28, 0, $stepper . "\n");
file_put_contents($file, implode('', $lines));
