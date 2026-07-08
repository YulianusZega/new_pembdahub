<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
try {
    $req = Illuminate\Http\Request::create('/admin/surveys', 'GET');
    $req->setUserResolver(function () {
        $user = \App\Models\User::where('role', 'super_admin')->first();
        $user->must_change_password = false;
        return $user;
    });
    $res = $kernel->handle($req);
    echo $res->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
