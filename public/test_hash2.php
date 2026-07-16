<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = App\Models\User::first();
$user->password = "Test1234";
$user->save();

$freshUser = App\Models\User::find($user->id);
echo "Password field after saving raw string: " . $freshUser->password . "\n";
echo "Is it a bcrypt hash? " . (str_starts_with($freshUser->password, "$2y$") ? "YES" : "NO") . "\n";

