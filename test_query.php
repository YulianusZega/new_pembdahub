<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = App\Models\User::query(); 
$q->where(function($q2) { 
    $q2->where('role', 'kepala_sekolah')
       ->orWhereIn('id', function($sub) { 
           $sub->select('user_id')
               ->from('teachers')
               ->whereIn('id', function($sub2) { 
                   $sub2->select('principal_id')
                        ->from('schools')
                        ->whereNotNull('principal_id')
                        ->where('type', '!=', 'YAYASAN'); 
               }); 
       }); 
}); 
echo $q->toSql();
